<?php


namespace App\Services;


use App\Exceptions\Charge\CantCancelAlreadyPaidCharge;
use App\Exceptions\Charge\ChargeAlreadyExpired;
use App\Exceptions\Charge\InvalidTransactionForChargeConfirmation;
use App\Integrations\Juno\Models\Billing;
use App\Models\Charge;
use App\Models\CreditCard;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\QRCode\QRCodeService;
use Carbon\Carbon;
use chillerlan\QRCode\Data\QRCodeDataException;
use chillerlan\QRCode\Output\QRCodeOutputException;
use chillerlan\QRCode\QRCodeException;
use Exception;
use Lifepet\Utils\Date;
use Webpatser\Uuid\Uuid;

class ChargeService
{
    private $qrcodeService;

    public function __construct()
    {
        $this->qrcodeService = new QRCodeService();
    }

    public function fromReference(string $reference): ?Charge
    {
        return Charge::reference($reference)->first();
    }

    /**
     * @throws Exception
     */
    public function open(Wallet $to, int $amount, Wallet $from = null, $base_url = null, $overwritable = 1, $tax = null, $cashback = null, $expires_at = null): ?Charge
    {
        $charge = new Charge();
        $reference = $this->generateReference();

        if(is_null($tax)) {
            $tax = $to->tax;
        }

        if(is_null($cashback)) {
            $cashback = $to->cashback;
        }

        $charge->forceFill([
            'reference' => $reference,
            'from_id' => $from ? $from->id : null,
            'to_id' => $to->id,
            'amount' => $amount,
            'status' => Charge::STATUS__OPEN,
            'expires_at' => now()->addHour(),
            'overwritable' => $overwritable,
            'tax' => $tax,
            'cashback' => $cashback
        ]);

        $charge->save();
        $this->makeChargeUrl($charge, $base_url);

        return $charge;
    }

    /**
     * @param Charge $charge
     * @param Transaction $transaction
     * @return Charge
     * @throws ChargeAlreadyExpired
     * @throws InvalidTransactionForChargeConfirmation
     */
    public function pay(Charge $charge, Transaction $transaction)
    {
        if($transaction->status !== Transaction::STATUS__SUCCESS) {
            throw new InvalidTransactionForChargeConfirmation();
        }

        if(now()->gte($charge->expires_at)) {
            throw new ChargeAlreadyExpired();
        }

        $charge->transaction_id = $transaction->id;
        $charge->status = Charge::STATUS__PAID;
        $charge->update();

        return $charge;
    }

    /**
     * @param Charge $charge
     * @throws CantCancelAlreadyPaidCharge
     */
    public function cancel(Charge $charge)
    {
        if($charge->status === Charge::STATUS__PAID) {
            throw new CantCancelAlreadyPaidCharge();
        }
        $charge->status = Charge::STATUS__CANCELED;
        $charge->update();
    }

    /**
     * @param Charge $charge
     * @return string
     * @throws QRCodeDataException
     * @throws QRCodeOutputException
     * @throws QRCodeException
     */
    public function qrcode(Charge $charge, $base64 = true): string
    {
        return $this->qrcodeService->render($charge->url, $base64);
    }

    /**
     * @throws Exception
     */
    private function generateReference(): string
    {
        return (string) Uuid::generate(4);
    }

    private function makeChargeUrl(Charge $charge, string $base_url = null) {
        if(!$base_url) {
            $url = route('charge.info', [
                'reference' => $charge->reference,
            ]);
        } else {
            $lastchar = $base_url[-1];
            if (strcmp($lastchar, "/") !== 0) {
               $base_url .= "/";
            }
            $url = $base_url . $charge->reference;
        }

        $charge->url = $url;
        $charge->update();
    }

    public function convertJunoEmbeddedToOpenPayment(Wallet $wallet, object $embedded, int $paymentType, \App\Integrations\Juno\Models\Charge $charge, Billing $billing, int $balanceAmount, int $amountToTransfer, CreditCard $card = null): Payment
    {
        $junoCharge = $embedded->charges[0];

        $payment = new Payment();
        $payment->payment_type = $paymentType;
        $payment->amount = $charge->getTotalAmount() * 100;
        $payment->original_amount = $amountToTransfer - $balanceAmount;
        $payment->installments = $charge->getInstallments();
        $payment->amount_installments = $payment->amount / $payment->installments;
        $payment->status = Payment::STATUS__OPEN;
        $payment->manager = CreditCard::MANAGER__JUNO;

        $address = $billing->getAddress();
        $payment->street = $address->getStreet();
        $payment->number = $address->getNumber();
        $payment->complement = $address->getComplement();
        $payment->neighborhood = $address->getNeighborhood();
        $payment->city = $address->getCity();
        $payment->state = $address->getState();
        $payment->post_code = $address->getPostCode();

        $payment->external_charge_id = $junoCharge->id;
        $payment->external_checkout_url = $junoCharge->checkoutUrl;

        $payment->wallet_id = $wallet->id;

        if($card) {
            $payment->card_id = $card->id;
        }

        $payment->save();
        return $payment;
    }

    public function confirmJunoPayment(Payment $payment, object $paymentResponse): Payment
    {
        $payment->external_transaction_id = $paymentResponse->transactionId;
        $payment->external_payment_id = $paymentResponse->payments[0]->id;
        $payment->external_fee = $paymentResponse->payments[0]->fee;
        $payment->external_release_date = Carbon::createFromFormat(Date::UTC_DATE, $paymentResponse->payments[0]->releaseDate);
        $status = $paymentResponse->payments[0]->status;

        if($status === "CONFIRMED") {
            $payment->status = Payment::STATUS_CONFIRMED;
            $payment->paid_at = now();
        } else {
            $payment->status = Payment::STATUS__FAIL;
            $payment->fail_reason = $paymentResponse->payments[0]->failReason;
        }

        $payment->update();
        return $payment;
    }
}
