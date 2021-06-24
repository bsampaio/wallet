<?php


namespace App\Exceptions\CreditCard;


use Throwable;

class CreditCardAmountShouldBeGreaterOrEqualTotalAmount extends \Exception
{
    const MESSAGE = "When you don't use balance, the credit card amount should be greater or equal the total amount.";

    public function __construct(int $creditCardAmount, int $amountToTransfer)
    {
        $diff = " { \$creditCardAmount ($creditCardAmount) < \$amountToTransfer ($amountToTransfer) }";
        parent::__construct(self::MESSAGE . $diff, 0, null);
    }
}
