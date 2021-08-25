<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/08/2021
 * Time: 14:51
 */

namespace App\Services;


use App\Models\DigitalAccount;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Webhook;
use App\Models\Withdraw;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webpatser\Uuid\Uuid;

class TransferService
{
    const TRANSFER_TYPE__DEFAULT = 'DEFAULT_BANK_ACCOUNT';
    const TRANSFER_TYPE__P2P = 'P2P';
    const TRANSFER_TYPE__BANK_ACCOUNT = 'BANK_ACCOUNT';
    const TRANSFER_TYPE__PIX = 'PIX';

    const EVENT__TRANSFER_STATUS_CHANGED = 'TRANSFER_STATUS_CHANGED';
    const EVENT__P2P_TRANSFER_STATUS_CHANGED = 'P2P_TRANSFER_STATUS_CHANGED';
    const THERE_WAS_AN_ERROR_TRYING_TO_CREATE_A_TRANSFER = 'There was an error trying to create a transfer.';

    public function openWithdraw(Wallet $wallet, $amount, string $url): ?Withdraw
    {
        $this->registerWebhook($wallet, self::EVENT__TRANSFER_STATUS_CHANGED, $url);

        $junoService = new \App\Integrations\Juno\Services\TransferService([], $wallet->digitalAccount->external_resource_token);
        $type = self::TRANSFER_TYPE__DEFAULT;

        try {
            $transfer = $junoService->createTransfer([
                'type' => $type,
                'amount' => round($amount / 100, 2)
            ]);

            if(!$transfer) {
                return null;
            }

            return $this->createWithdraw($wallet, $amount, $transfer);
        } catch (GuzzleException $e) {
            Log::error(self::THERE_WAS_AN_ERROR_TRYING_TO_CREATE_A_TRANSFER, [
                'exception' => $e->getMessage(),
                'wallet' => $wallet
            ]);
            return null;
        }
    }

    public function authorizeWithdraw(Withdraw $withdraw): Withdraw
    {
        if($withdraw->authorized) {
            $withdraw->authorization_code = (string) Uuid::generate(4);
            $withdraw->authorized_at = now();
            $withdraw->update();
        }
        return $withdraw;
    }

    public function processAuthorizedWithdraw(Withdraw $withdraw): bool
    {
        $logIdentifier = 'transfers.withdraw.process';
        if(!$withdraw->authorized || $withdraw->processed_at) {
            Log::error($logIdentifier . ' - Can\'t process an unauthorized withdraw. ID #'. $withdraw->id);
            return false;
        }
        $walletService = new WalletService();

        DB::beginTransaction();
        try {
            /**
             * @var Withdraw $withdraw
             */
            $withdraw = Withdraw::query()->lockForUpdate()->find($withdraw->id);
            /**
             * @var Wallet $wallet
             */
            $wallet = $withdraw->wallet()->lockForUpdate()->first();
            $amount = $withdraw->amount;
            $walletService->updateBalance($wallet, -$amount);
            $withdraw->processed_at = now();
            $withdraw->update();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($logIdentifier . ' - Can\'t process an unauthorized withdraw. ID #'. $withdraw->id, [
                'exception' => $e
            ]);
            return false;
        }
    }

    /**
     * @param Wallet $wallet
     * @param $amount
     * @param $transfer
     * @return Withdraw
     */
    private function createWithdraw(Wallet $wallet, $amount, $transfer): Withdraw
    {
        $withdraw = new Withdraw();
        $withdraw->amount = $amount;
        $withdraw->authorized = false;
        $withdraw->external_id = $transfer->id;
        $withdraw->external_status = Withdraw::STATUS__REQUESTED;
        $withdraw->external_digital_account_id = $wallet->digitalAccount->external_id;
        $withdraw->save();
        return $withdraw;
    }

    public function openP2pTransfer(Transaction $transaction, string $url): ?Transfer
    {
        $this->registerWebhook($transaction->to, self::EVENT__P2P_TRANSFER_STATUS_CHANGED, $url);

        $junoService = new \App\Integrations\Juno\Services\TransferService([], $transaction->to->digitalAccount->external_resource_token);
        $type = self::TRANSFER_TYPE__P2P;

        try {
            $transfer = $junoService->createTransfer([
                'type' => $type,
                'name' => $transaction->to->digitalAccount->name,
                'document' => $transaction->to->digitalAccount->document,
                'amount' => $transaction->balance_amount / 100,
                'bankAccount' => [
                    'accountNumber' => $transaction->to->digitalAccount->external_account_number
                ]
            ]);

            if(!$transfer) {
                return null;
            }

            return $this->createP2pTransfer($transaction, $transfer);
        } catch (GuzzleException $e) {
            Log::error(self::THERE_WAS_AN_ERROR_TRYING_TO_CREATE_A_TRANSFER, [
                'exception' => $e->getMessage(),
                'wallet' => $transaction->to
            ]);
            return null;
        }
    }

    public function authorizeTransfer(Transfer $transfer): Transfer
    {
        if($transfer->authorized) {
            $transfer->authorization_code = (string) Uuid::generate(4);
            $transfer->authorized_at = now();
            $transfer->update();
        }
        return $transfer;
    }

    private function createP2pTransfer(Transaction $transaction, $transfer): Transfer
    {
        $transfer = new Transfer();
        $transfer->amount = $transaction->balance_amount;
        $transfer->wallet_id = $transaction->to->id;
        $transfer->transaction_id = $transaction->id;
        $transfer->authorization_code = null;
        $transfer->external_status = Transfer::STATUS__REQUESTED;
        $transfer->external_digital_account_id = $transaction->to->digitalAccount->external_id;

        return $transfer;
    }

    public function processAuthorizedTransfer(Transfer $transfer): bool
    {
        $logIdentifier = 'transfers.transfer.process';
        if(!$transfer->authorized || $transfer->processed_at) {
            Log::error($logIdentifier . ' - Can\'t process an unauthorized transfer. ID #'. $transfer->id);
            return false;
        }
        $walletService = new WalletService();

        DB::beginTransaction();
        try {
            $transfer = Transfer::query()->lockForUpdate()->find($transfer->id);
            /**
             * @var Wallet $wallet
             * @var Wallet $lifepetWallet
             */
            $wallet = $transfer->wallet()->lockForUpdate()->first();
            $lifepetWallet = User::master()->lockForUpdate()->first()->wallet;
            $amount = $transfer->amount;

            $walletService->updateBalance($lifepetWallet, -$amount);
            $walletService->updateBalance($wallet, $amount);

            $transfer->processed_at = now();
            $transfer->update();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($logIdentifier . ' - Can\'t process transfer. There was an unknown error. ID #'. $transfer->id, [
                'exception' => $e
            ]);
            return false;
        }
    }

    private function registerWebhook(Wallet $wallet, $event, $url): void
    {
        $digitalAccount = $wallet->digitalAccount;
        if(!$digitalAccount || $digitalAccount->disabled()) {
            return;
        }

        $webhook = Webhook::fromWallet($wallet)->event($event)->first();
        if(!$webhook) {
            //Register webhooks
            $webhooksService = new \App\Integrations\Juno\Services\WebhookService([], $digitalAccount->external_resource_token);
            $webhookResponse = $webhooksService->register([
                'url' => $url,
                'eventTypes' => [
                    $event,
                ]
            ]);

            if(isset($webhookResponse->secret)) {
                $webhook = new Webhook();
                $webhook->wallet_id = $wallet->id;
                $webhook->event = $event;
                $webhook->status = 'ACTIVE';
                $webhook->secret = $webhookResponse->secret;
                $webhook->url = $webhookResponse->url;
                $webhook->save();
            }
        }

    }
}
