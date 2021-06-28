<?php


namespace App\Exceptions\Charge;


use Throwable;

class IncorrectReceiverOnTransfer extends \Exception
{
    const MESSAGE = "The given receiver of transfer is not the expected.";

    public function __construct($given, $expected)
    {
        parent::__construct(self::MESSAGE . " given: $given, expected: $expected", 0, null);
    }
}
