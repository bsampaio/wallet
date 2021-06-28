<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultWallets extends Migration
{
    public $wallets = null;

    public function __construct()
    {
        $this->wallets  = [
            [
                'user' => [
                    'name' => 'Lifepet Saúde',
                    'nickname' => 'lifepet',
                    'email' => 'lifepet@lifepet.com.br',
                    'password' => bcrypt('l1f3p3t')
                ],
                'wallet' => [
                    'balance' => 10000,
                    'active' => 1,
                    'type' => \App\Models\Wallet::TYPE__BUSINESS,
                    'cashback' => 0,
                    'tax' => 0
                ]
            ],
            [
                'user' => [
                    'name' => 'Customer Simulation',
                    'nickname' => 'customer',
                    'email' => 'customer@lifepet.com.br',
                    'password' => bcrypt('l1f3p3tcust0m3r')
                ],
                'wallet' => [
                    'balance' => 10000,
                    'active' => 1,
                    'type' => \App\Models\Wallet::TYPE__PERSONAL,
                    'cashback' => 0,
                    'tax' => 0
                ]
            ],
            [
                'user' => [
                    'name' => 'Partner Simulation',
                    'nickname' => 'partner',
                    'email' => 'partner@lifepet.com.br',
                    'password' => bcrypt('l1f3p3tp4rtn3r')
                ],
                'wallet' => [
                    'balance' => 10000,
                    'active' => 1,
                    'type' => \App\Models\Wallet::TYPE__BUSINESS,
                    'cashback' => 20,
                    'tax' => 5
                ]
            ]
        ];
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $walletService = new \App\Services\WalletService();
        foreach($this->wallets as $w) {
            $exists = \App\Models\User::nickname($w['user']['nickname'])->exists();
            if($exists) {
                continue;
            }
            $user = new \App\Models\User();
            $user->forceFill($w['user']);
            $user->save();

            $wallet = new \App\Models\Wallet();
            $wallet->forceFill($w['wallet']);
            $wallet->user_id = $user->id;
            $wallet->save();

            $walletService->ensuredKeyGeneration($wallet);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach($this->wallets as $w) {
            $exists = \App\Models\User::nickname($w['user']['nickname'])->exists();
            if($exists) {
                $user = \App\Models\User::nickname($w['user']['nickname'])->first();
                $user->wallet->delete();
                $user->delete();
            }
        }
    }
}
