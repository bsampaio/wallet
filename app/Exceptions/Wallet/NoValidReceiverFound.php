<?php


namespace App\Exceptions\Wallet;


use Throwable;

class NoValidReceiverFound extends \Exception
{
    const MESSAGE = 'No active or valid receiver wallet was found.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 0, null);
    }
}
