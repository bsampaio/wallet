<?php

namespace App\Exceptions\Charge;

class ChargeAlreadyExpired extends \Exception
{

    const MESSAGE = "Can't pay an expired charge.";

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0, null);
    }
}
