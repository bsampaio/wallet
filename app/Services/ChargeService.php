<?php


namespace App\Services;


use App\Exceptions\Charge\CantCancelAlreadyPaidCharge;
use App\Exceptions\Charge\ChargeAlreadyExpired;
use App\Exceptions\Charge\InvalidTransactionForChargeConfirmation;
use App\Models\Charge;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\QRCode\QRCodeService;
use chillerlan\QRCode\Data\QRCodeDataException;
use chillerlan\QRCode\Output\QRCodeOutputException;
use chillerlan\QRCode\QRCodeException;
use Exception;
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
    public function open(Wallet $from, Wallet $to, int $amount, $expires_at = null): ?Charge
    {
        $charge = new Charge();

        $charge->forceFill([
            'reference' => $this->generateReference(),
            'from_id' => $from->id,
            'to_id' => $to->id,
            'amount' => $amount,
            'status' => Charge::STATUS__OPEN,
            'expires_at' => now()->addHour()
        ]);

        $charge->save();

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
}
