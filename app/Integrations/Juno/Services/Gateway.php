<?php


namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Models\Address;
use App\Integrations\Juno\Models\Billing;
use App\Integrations\Juno\Models\Charge;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;

class Gateway
{
    private $chargeService;
    private $paymentService;

    public function __construct()
    {
        $this->chargeService = new ChargeService();
        $this->paymentService = new PaymentService();
    }

    public function buildCharge(string $description, float $totalAmount, int $installments = 0, Carbon $dueDate = null, array $paymentTypes = [], bool $pix = false): Charge
    {
        return new Charge($description, $totalAmount, $installments, $dueDate, $paymentTypes, $pix);
    }

    public function buildBilling(string $name, string $document, string $email, string $phone, Carbon $birthDate, Address $address): Billing
    {
        return new Billing($name, $document, $email, $phone, $birthDate, $address);
    }

    public function buildAddress(string $street, string $number, string $neighborhood, string $city, string $state, string $postCode, string $complement = null): Address
    {
        return new Address($street, $number, $neighborhood, $city, $state, $postCode, $complement);
    }

    /**
     * @throws GuzzleException
     */
    public function charge(Charge $charge, Billing $billing)
    {
        $params = [
            'charge' => $charge->toArray(),
            'billing' => $billing->toArray()
        ];

        return $this->chargeService->createCharge($params);
    }

    /**
     * @throws GuzzleException
     */
    public function pay(string $chargeId, array $billing, string $cardStoredHash)
    {
        return $this->paymentService->createPayment([
            'chargeId' => $chargeId,
            'billing' => $billing,
            'creditCardDetails' => [
                'creditCardId' => $cardStoredHash
            ]
        ]);
    }
}
