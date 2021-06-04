<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/06/2021
 * Time: 16:10
 */

namespace App\Services;


use App\Models\User;
use App\Models\Wallet;
use App\Utils\Number;

class WalletService
{
    public function __construct()
    {
        //TODO: Initialize Payment Gateway services.
    }

    public function enable(User $user) {
        $wallet = $user->wallet;
        if(!$wallet) {
            $wallet = new Wallet();
            $wallet->user_id = $user->id;
            if(!$wallet->save()) {
                return null;
            }
        }

        return $wallet;
    }

    public function getBalance($wallet)
    {
        $balance = 0;
        if($wallet) {
            $balance = $wallet->balance;
        }

        return [
            'money' => Number::money($balance),
            'value' => $balance
        ];
    }
}