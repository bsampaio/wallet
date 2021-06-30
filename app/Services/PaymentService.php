<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 30/06/2021
 * Time: 11:32
 */

namespace App\Services;


use App\Integrations\Juno\Services\Pix;
use App\Models\Wallet;

class PaymentService
{
    /**
     * @param Wallet $wallet
     * @param int $amountToTransfer
     * @param int $tax
     */
    public function verifyPixDeposit(Wallet $wallet, int $amountToTransfer, int $tax)
    {


    }

    public function generateRandomPixKey()
    {
        $pixService = new Pix();
        dd($pixService->createRandomKey(null, null, []));
    }
}