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
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class TransferService
{
    const TRANSFER_TYPE__DEFAULT = 'DEFAULT_BANK_ACCOUNT';
    const TRANSFER_TYPE__P2P = 'P2P';
    const TRANSFER_TYPE__BANK_ACCOUNT = 'BANK_ACCOUNT';
    const TRANSFER_TYPE__PIX = 'PIX';

    public function openWithdraw(Wallet $wallet, $amount)
    {
        //Check webhook


        $junoService = new \App\Integrations\Juno\Services\TransferService([], $wallet->digitalAccount->external_resource_token);
        $type = self::TRANSFER_TYPE__DEFAULT;

        try {
            $transfer = $junoService->createTransfer([
                'type' => $type
            ]);

            return $transfer;
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

    public function changeStatus()
    {

    }
}