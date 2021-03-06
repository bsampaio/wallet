<?php


namespace App\Services;


use App\Exceptions\Charge\AmountTransferedIsDifferentOfCharged;
use App\Exceptions\Charge\ChargeAlreadyExpired;
use App\Exceptions\Charge\ChargeAlreadyPaid;
use App\Exceptions\Charge\IncorrectReceiverOnTransfer;
use App\Exceptions\Charge\InvalidChargeReference;
use App\Exceptions\CreditCard\CreditCardAmountShouldBeGreaterOrEqualTotalAmount;
use App\Exceptions\CreditCard\CreditCardUseIsRequired;
use App\Exceptions\CreditCard\InstallmentDoesntReachMinimumValue;
use App\Exceptions\CreditCard\ReceiverDigitalAccountNotEnabled;
use App\Exceptions\Wallet\AmountSumIsLowerThanTotalTransfer;
use App\Exceptions\Wallet\NoValidReceiverFound;
use App\Models\CreditCard;
use App\Models\Wallet;

class CreditCardService
{
    const MINIMUM_AMOUNT_VALUE_IN_CENTS = 1000;

    public function __construct()
    {
    }

    public function main(Wallet $wallet): ?CreditCard
    {
        //First main active card
        return CreditCard::heldBy($wallet)->main()->first();
    }

    public function find(Wallet $wallet, int $id): ?CreditCard
    {
        //Active only
        return CreditCard::heldBy($wallet)->where('id', $id)->first();
    }

    /**
     * @throws AmountSumIsLowerThanTotalTransfer
     * @throws CreditCardUseIsRequired
     * @throws CreditCardAmountShouldBeGreaterOrEqualTotalAmount
     * @throws InstallmentDoesntReachMinimumValue
     * @throws IncorrectReceiverOnTransfer
     * @throws ChargeAlreadyPaid
     * @throws AmountTransferedIsDifferentOfCharged
     * @throws InvalidChargeReference
     * @throws ChargeAlreadyExpired
     * @throws ReceiverDigitalAccountNotEnabled
     */
    public function verifyCreditCardTransfer(Wallet $wallet, Wallet $receiver, bool $useBalance, int $balanceAmount, bool $useCreditCard, int $creditCardAmount, int $amountToTransfer, int $installments, string $reference)
    {
        if(!$useCreditCard) {
            throw new CreditCardUseIsRequired();
        }

        if(!$receiver->digitalAccount || $receiver->digitalAccount->disabled()) {
            throw new ReceiverDigitalAccountNotEnabled();
        }

        if($balanceAmount + $creditCardAmount < $amountToTransfer) {
            throw new AmountSumIsLowerThanTotalTransfer($balanceAmount, $creditCardAmount, $amountToTransfer);
        }

        if(!$useBalance) {
            if($creditCardAmount < $amountToTransfer) {
                throw new CreditCardAmountShouldBeGreaterOrEqualTotalAmount($creditCardAmount, $amountToTransfer);
            }
        }
        $amountInstallment = ($creditCardAmount / $installments);
        if($amountInstallment < self::MINIMUM_AMOUNT_VALUE_IN_CENTS) {
            throw new InstallmentDoesntReachMinimumValue($amountInstallment, self::MINIMUM_AMOUNT_VALUE_IN_CENTS);
        }

        if($reference) {
            (new WalletService())->authorizeChargePayment($reference, $amountToTransfer, $receiver, null);
        }
    }
}
