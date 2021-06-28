<?php


namespace App\Exceptions\Wallet;


use Throwable;

class CantTransferToYourself extends \Exception
{
    const MESSAGE = 'You can\'t make a transfer to yourself.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0, null);
    }
}
