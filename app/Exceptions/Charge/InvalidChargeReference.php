<?php


namespace App\Exceptions\Charge;


use Throwable;

class InvalidChargeReference extends \Exception
{
    const MESSAGE = "The given reference isn't related to any charge.";

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0, null);
    }
}
