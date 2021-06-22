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

    public function __construct()
    {
        $this->chargeService = new ChargeService();
    }

    public function buildCharge(string $description, float $totalAmount, int $installments = 0, Carbon $dueDate = null): Charge
    {
        return new Charge($description, $totalAmount, $installments, $dueDate);
    }

    public function buildBilling(string $name, string $document, string $email, string $phone, Carbon $birthDate, Address $address): Billing
    {
        return new Billing($name, $document, $email, $phone, $birthDate, $address);
    }

    public function buildAddress(string $street, string $number, string $neighbourhood, string $city, string $state, string $postCode, string $complement = null): Address
    {
        return new Address($street, $number, $neighbourhood, $city, $state, $postCode, $complement);
    }

    /**
     * @throws GuzzleException
     */
    public function charge(Charge $charge, Billing $billing)
    {
        return $this->chargeService->createCharge([
            'charge' => $charge->toArray(),
            'billing' => $billing->toArray()
        ]);
    }
}
