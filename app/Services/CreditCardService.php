<?php


namespace App\Services;


use App\Models\CreditCard;
use App\Models\Wallet;

class CreditCardService
{
    public function __construct()
    {
    }

    public function main(Wallet $wallet): ?CreditCard
    {
        //First main active card
        return CreditCard::heldBy($wallet)->main()->first();
    }

    public function find(Wallet $wallet, int $id): ?CreditCard
    {
        //Active only
        return CreditCard::heldBy($wallet)->where('id', $id)->first();
    }
}
