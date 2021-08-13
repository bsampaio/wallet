<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 27/07/2021
 * Time: 15:01
 */

namespace App\Services\Notification;


use App\Models\Transfer;
use App\Models\Wallet;
use App\Models\Withdraw;

class PartnerNotificationService extends HttpNotificationService
{
    public function __construct(array $headers = [])
    {
        $headers = array_merge([
            'X-App-Secret' => env('PARTNER_APP_SECRET', '93aa11b50fd38957904fa051ca0cfb23cc2e8e62')
        ], $headers);
        $base_uri = env('PARTNER_BASE_URI', 'https://partner-staging.lifepet.com.br');
        parent::__construct($base_uri, $headers);
    }

    /**
     * @param Wallet $wallet
     * @param string $status
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function digitalAccountStatusChanged(Wallet $wallet, string $status)
    {
        $url = 'api/digital_account/' . $wallet->user->nickname . '/status';
        $digitalAccountInfo = null;

        if($wallet->digitalAccount) {
            $digitalAccountInfo = [
                'status' => $wallet->digitalAccount->external_status,
                'external_id' => $wallet->digitalAccount->external_id,
            ];
        }

        return $this->client->post($url, [
            'json' => [
                'digitalAccount' => $digitalAccountInfo
            ]
        ]);
    }

    public function withdrawStatusChanged(Wallet $wallet, Withdraw $withdraw, string $status)
    {
        $url = 'api/withdraw/' . $wallet->user->nickname . '/status';


        return $this->client->post($url, [
            'json' => [
                'id' => $withdraw->external_id,
                'status' => $status
            ]
        ]);
    }

    public function transferStatusChanged(Wallet $wallet, Transfer $transfer, string $status)
    {
        $url = 'api/transfer/' . $wallet->user->nickname . '/status';


        return $this->client->post($url, [
            'json' => [
                'id' => $transfer->external_id,
                'status' => $status
            ]
        ]);
    }
}
