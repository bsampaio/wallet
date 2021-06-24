<?php


namespace App\Exceptions\CreditCard;


use Throwable;

class InstallmentDoesntReachMinimumValue extends \Exception
{
    const MESSAGE = "The installment doesn't reach the minimum value.";

    public function __construct($installmentAmount, $minimumValue)
    {
        $diff = " { \$installmentAmount ($installmentAmount$) < \$minimumValue ($minimumValue) }";
        parent::__construct(self::MESSAGE . $diff, 0, null);
    }
}
