<?php


namespace App\Exceptions\Charge;


use Throwable;

class InvalidTransactionForChargeConfirmation extends \Exception
{
    const MESSAGE = "The given Transaction object isn't valid to confirm a charge payment.";

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0, null);
    }
}
