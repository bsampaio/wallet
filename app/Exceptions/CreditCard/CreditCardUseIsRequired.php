<?php


namespace App\Exceptions\CreditCard;


use Throwable;

class CreditCardUseIsRequired extends \Exception
{
    const MESSAGE = "You need to use credit card on a 'credit card' payment.";

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0, null);
    }
}
