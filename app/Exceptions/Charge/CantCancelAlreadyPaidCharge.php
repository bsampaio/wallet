<?php


namespace App\Exceptions\Charge;


class CantCancelAlreadyPaidCharge extends \Exception
{

    const MESSAGE = "Can't cancel an paid charge.";

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0, null);
    }
}
