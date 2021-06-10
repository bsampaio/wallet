<?php


namespace App\Exceptions\Wallet;


use Throwable;

class AmountLowerThanMinimum extends \Exception
{
    const MESSAGE = "The total amount is lower than 1 cent and it's invalid.";

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0, null);
    }
}
