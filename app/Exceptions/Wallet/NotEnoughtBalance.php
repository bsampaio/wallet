<?php


namespace App\Exceptions\Wallet;


use Throwable;

class NotEnoughtBalance extends \Exception
{
    const MESSAGE = "There is no balance enought to do the transfer.";

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0, null);
    }
}
