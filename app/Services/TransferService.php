<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/08/2021
 * Time: 14:51
 */

namespace App\Services;


use App\Models\DigitalAccount;
use App\Models\Wallet;
use App\Models\Webhook;
use App\Models\Withdraw;
use GuzzleHttp\Exception\GuzzleException;
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

    public function openWithdraw(Wallet $wallet, $amount, string $url)
    {
        $this->registerWebhook($wallet, self::EVENT__TRANSFER_STATUS_CHANGED, $url);

        $junoService = new \App\Integrations\Juno\Services\TransferService([], $wallet->digitalAccount->external_resource_token);
        $type = self::TRANSFER_TYPE__DEFAULT;

        try {
            $transfer = $junoService->createTransfer([
                'type' => $type,
                'amount' => $amount / 100
            ]);

            if(!$transfer) {
                return false;
            }

            $withdraw = $this->createWithdraw($wallet, $amount, $transfer);

            return $withdraw;
        } catch (GuzzleException $e) {
            Log::error('There was an error trying to create a transfer.', [
                'exception' => $e->getMessage(),
                'wallet' => $wallet
            ]);
            return null;
        }
    }

    private function registerWebhook(Wallet $wallet, $event, $url): bool
    {
        $digitalAccount = $wallet->digitalAccount;
        if(!$digitalAccount || $digitalAccount->disabled()) {
            return false;
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

        return true;
    }

    public function authorizeWithdraw(Withdraw $withdraw)
    {
        if($withdraw->authorized) {
            $withdraw->authorization_code = (string) Uuid::generate(4);
            $withdraw->authorized_at = now();
            $withdraw->update();
        }
        return $withdraw;
    }

    public function processAuthorizedWithdraw(Withdraw $withdraw)
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
            $amount = $withdraw->amount / 100;
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
        $wallet->save();
        return $withdraw;
    }
}