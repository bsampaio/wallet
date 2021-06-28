<?php

namespace App\Exceptions\Charge;

class ChargeAlreadyPaid extends \Exception
{

    const MESSAGE = "Can't pay an already paid charge.";

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0, null);
    }
}
