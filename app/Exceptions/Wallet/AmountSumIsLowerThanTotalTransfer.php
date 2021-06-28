<?php


namespace App\Exceptions\Wallet;


use Throwable;

class AmountSumIsLowerThanTotalTransfer extends \Exception
{
    const MESSAGE = "The sum of BALANCE and CREDIT_CARD amounts doesn't reach the total amount requested for transfer.";

    public function __construct(int $balanceAmount, int $creditCardAmount, int $amountToTransfer)
    {
        $diff = " { \$balanceAmount($balanceAmount) + \$creditCardAmount($creditCardAmount) < \$amountToTransfer($amountToTransfer) }";
        parent::__construct(self::MESSAGE . $diff, 0, null);
    }
}
