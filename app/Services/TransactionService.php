<?php


namespace App\Services;


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
     * @param null $scheduled_to
     * @return Transaction
     * @throws Exception
     */
    public function transfer(Wallet $from, Wallet $to, int $amount, $description = null, $scheduled_to = null): Transaction
    {
        $order = $this->generateOrder();
        $transaction = new Transaction();
        DB::beginTransaction();
        try {
            $transaction->order = $order;
            $transaction->amount = $amount;
            $transaction->description = $description;
            $transaction->from_id = $from->id;
            $transaction->to_id = $to->id;
            $transaction->scheduled_to = $scheduled_to;
            $transaction->status = Transaction::STATUS__SUCCESS;
            $transaction->confirmed_at = now();

            if($transaction->scheduled_to) {
                $transaction->confirmed_at = null;
                $transaction->status = Transaction::STATUS__SCHEDULED;
            }

            $transaction->save();

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
}
