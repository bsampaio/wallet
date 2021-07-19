<?php


namespace App\Exceptions\CreditCard;


use Throwable;

class ReceiverDigitalAccountNotEnabled extends \Exception
{
    const MESSAGE = "The receiver of credit card transactions should have an open and enabled digital account.";

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0, null);
    }
}
