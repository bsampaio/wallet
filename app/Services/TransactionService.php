<?php


namespace App\Services;


use App\Models\Charge;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webpatser\Uuid\Uuid;

class TransactionService
{
    /**
     * @param Wallet $from
     * @param Wallet $to
     * @param int $amount
     * @param null $description
     * @param null $reference
     * @param null $tax
     * @param null $cashback
     * @param null $scheduled_to
     * @return Transaction
     * @throws Exception
     */
    public function transfer(Wallet $from, Wallet $to, int $amount, $description = null, $reference = null, $tax = null, $cashback = null, $compensateAfter = 0, $scheduled_to = null): Transaction
    {
        $order = $this->generateOrder();
        $transaction = new Transaction();
        DB::beginTransaction();
        try {
            $this->buildTransaction($order, $transaction, $amount, $description, $from, $to, $reference, $compensateAfter, $scheduled_to);

            $this->updateCharge($transaction);

            $this->updateBalance($from, -$amount);
            if(!$compensateAfter) {
                $this->updateBalance($to, $amount);
            }

            $tax = $this->getTax($transaction, $tax);
            $this->applyTaxes($transaction, $tax);

            $cashback = $this->getCashback($transaction, $cashback);
            $this->applyCashback($transaction, $cashback);

            DB::commit();

            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function compensate(Transaction $transaction): bool
    {
        $logNamespace = 'Wallet.Transactions.Compensate - ';
        Log::info($logNamespace . 'Starting to try to compensate the transaction #' . $transaction->order, ['transaction' => $transaction, 'time' => now()]);

        if($transaction->shouldBeCompensated()) {
            DB::beginTransaction();
            try {
                $this->updateBalance($transaction->to, $transaction->amount);
                $transaction->status = Transaction::STATUS__SUCCESS;
                $transaction->update();
                DB::commit();
                Log::info($logNamespace . 'Success in compensate the transaction #' . $transaction->order, ['transaction' => $transaction, 'time' => now()]);
                return true;
            } catch (Exception $e) {
                DB::rollBack();
                Log::error($logNamespace . 'Error on trying to compensate the transaction #' . $transaction->order, ['transaction' => $transaction, 'time' => now(), 'exception' => $e]);
                return false;
            }
        }

        return true;
    }

    /**
     * @throws Exception
     */
    private function generateOrder(): string
    {
        return (string) Uuid::generate(4);
    }


    /**
     * Updates wallet amount after transaction
     * @param Wallet $wallet
     * @param int $amount Amount in cents
     */
    private function updateBalance(Wallet $wallet, int $amount)
    {
        $wallet->balance = $wallet->balance + ($amount/100);
        $wallet->update();
    }

    /**
     * @param string $order
     * @param Transaction $transaction
     * @param int $amount
     * @param $description
     * @param Wallet $from
     * @param Wallet $to
     * @param $reference
     * @param int $compensateAfter
     * @param $scheduled_to
     * @param Payment|null $payment
     */
    private function buildTransaction(string $order, Transaction $transaction, int $amount, $description, Wallet $from, Wallet $to, $reference, int $compensateAfter, $scheduled_to, Payment $payment = null): void
    {
        $transaction->order = $order;
        $transaction->amount = $amount;
        $transaction->balance_amount = $amount;
        $transaction->payment_amount = $amount;
        $transaction->description = $description;
        $transaction->from_id = $from->id;
        $transaction->to_id = $to->id;
        $transaction->scheduled_to = $scheduled_to;
        $transaction->status = Transaction::STATUS__SUCCESS;
        $transaction->confirmed_at = now();
        $transaction->type = Transaction::TYPE__TRANSFER;

        if ($reference) {
            $transaction->type = Transaction::TYPE__CHARGE;
            $transaction->charge_id = Charge::reference($reference)->first()->id;
        }

        if ($transaction->scheduled_to) {
            $transaction->confirmed_at = null;
            $transaction->status = Transaction::STATUS__SCHEDULED;
        }

        $transaction->compensate_at = now()->addDays($compensateAfter);
        if($compensateAfter > 0) {
            $transaction->compensate_at = $transaction->compensate_at->startOfHour();
            $transaction->status = Transaction::STATUS__WAITING;
        }

        if($payment) {
            $transaction->payment_id = $payment->id;
            $transaction->payment_amount = $payment->amount;
            $transaction->balance_amount -= $payment->amount;
        }

        $transaction->save();
    }

    /**
     * @param Transaction $transaction
     */
    private function updateCharge(Transaction $transaction): void
    {
        if ($transaction->charge) {
            $transaction->charge->transaction_id = $transaction->id;
            $transaction->charge->status = Charge::STATUS__PAID;
            $transaction->charge->update();
        }
    }

    private function applyTaxes(Transaction  $origin, $tax): void
    {
        /**
         * Only applies taxes if it's a business account
         */
        if($origin->to->personal) {
            return;
        }

        $tax = $tax ?: $origin->to->tax;
        $master = User::master()->first();
        if(!$master) {
            Log::alert('The master user cannot be found on system.');
            return;
        }

        if(!$tax) {
            Log::info("There is no TAX for the transaction identified by {$origin->order}.");
            return;
        }

        $amount = ($tax/100) * $origin->amount;
        $taxTransaction = new Transaction();
        $taxTransaction->order = $this->generateOrder();
        $taxTransaction->amount = $amount;
        $taxTransaction->description = "Automatic tax of {$tax}%";
        $taxTransaction->from_id = $origin->to->id;
        $taxTransaction->to_id = $master->wallet->id;
        $taxTransaction->origin_id = $origin->id;
        $taxTransaction->type = Transaction::TYPE__TAX;
        $taxTransaction->status = Transaction::STATUS__SUCCESS;
        $taxTransaction->confirmed_at = now();

        $taxTransaction->save();

        $this->updateBalance($origin->to, -$amount);
        $this->updateBalance($master->wallet, $amount);
    }

    private function applyCashback(Transaction $origin, $cashback)
    {
        /**
         * Only generates cashback if it's a business account
         */
        if($origin->to->personal) {
            return;
        }
        $tax = $cashback;
        if(is_null($cashback)) {
            $tax = $origin->to->cashback;
        }

        if(!$tax) {
            Log::info("There is no CASHBACK for the transaction identified by {$origin->order}.");
            return;
        }

        $amount = ($tax/100) * $origin->amount;
        $cashbackTransaction = new Transaction();
        $cashbackTransaction->order = $this->generateOrder();
        $cashbackTransaction->amount = $amount;
        $cashbackTransaction->description = "Cashback of {$tax}%";
        $cashbackTransaction->from_id = $origin->to->id;
        $cashbackTransaction->to_id = $origin->from->id;
        $cashbackTransaction->origin_id = $origin->id;
        $cashbackTransaction->type = Transaction::TYPE__CASHBACK;
        $cashbackTransaction->status = Transaction::STATUS__SUCCESS;
        $cashbackTransaction->confirmed_at = now();
        $cashbackTransaction->save();

        $this->updateBalance($origin->to, -$amount);
        $this->updateBalance($origin->from, $amount);
    }

    /**
     * @param Wallet $from
     * @param Wallet $to
     * @param int $amountToTransfer
     * @param int $balanceAmount
     * @param Payment $payment
     * @param int $compensateAfter
     * @param null $description
     * @param null $reference
     * @param null $tax
     * @param null $cashback
     * @param null $scheduled_to
     * @return Transaction
     * @throws Exception
     */
    public function transferWithPayment(Wallet $from, Wallet $to, int $amountToTransfer, int $balanceAmount, Payment $payment, int $compensateAfter, $description = null, $reference = null, $tax = null, $cashback = null, $scheduled_to = null): Transaction
    {
        $order = $this->generateOrder();
        $transaction = new Transaction();
        DB::beginTransaction();
        try {
            $this->buildTransaction($order, $transaction, $amountToTransfer, $description, $from, $to, $reference, $compensateAfter, $scheduled_to, $payment);

            $this->updateCharge($transaction);

            $this->updateBalance($from, -$balanceAmount);
            if(!$compensateAfter) {
                $this->updateBalance($to, $amountToTransfer);
            }

            $tax = $this->getTax($transaction, $tax);
            $this->applyTaxes($transaction, $tax);

            $cashback = $this->getCashback($transaction, $cashback);
            $this->applyCashback($transaction, $cashback);

            DB::commit();

            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getTax(Transaction $transaction, int $tax)
    {
        $charge = $transaction->charge;
        if($charge && !$charge->overwritable) {
            return $charge->tax;
        }
        if(is_null($tax)) {
            return $transaction->to->tax;
        }
    }

    public function getCashback(Transaction $transaction, int $tax)
    {
        $charge = $transaction->charge;
        if($charge && !$charge->overwritable) {
            return $charge->cashback;
        }
        if(is_null($tax)) {
            return $transaction->to->cashback;
        }
    }
}
