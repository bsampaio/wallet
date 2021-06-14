<?php


namespace App\Services;


use App\Models\Charge;
use App\Models\Transaction;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;
use Webpatser\Uuid\Uuid;

class TransactionService
{
    /**
     * @param Wallet $from
     * @param Wallet $to
     * @param int $amount
     * @param null $description
     * @param null $reference
     * @param null $scheduled_to
     * @return Transaction
     * @throws Exception
     */
    public function transfer(Wallet $from, Wallet $to, int $amount, $description = null, $reference = null, $scheduled_to = null): Transaction
    {
        $order = $this->generateOrder();
        $transaction = new Transaction();
        DB::beginTransaction();
        try {
            $this->buildTransaction($order, $transaction, $amount, $description, $from, $to, $scheduled_to, $reference);

            $this->updateCharge($transaction);

            $this->updateBalance($from, -$amount);
            $this->updateBalance($to, $amount);

            DB::commit();

            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
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
     * @param $scheduled_to
     * @param $reference
     */
    private function buildTransaction(string $order, Transaction $transaction, int $amount, $description, Wallet $from, Wallet $to, $scheduled_to, $reference): void
    {
        $transaction->order = $order;
        $transaction->amount = $amount;
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
}
