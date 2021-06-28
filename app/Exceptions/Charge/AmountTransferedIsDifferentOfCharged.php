<?php


namespace App\Exceptions\Charge;


use Throwable;

class AmountTransferedIsDifferentOfCharged extends \Exception
{
    const MESSAGE = "The given amount is different of the amount charged.";

    public function __construct($amount, $charged)
    {
        parent::__construct(self::MESSAGE . " amount: $amount, charged: $charged", 0, null);
    }
}
