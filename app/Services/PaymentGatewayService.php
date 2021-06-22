<?php


namespace App\Services;


use App\Integrations\Juno\Services\ChargeService;
use App\Integrations\Juno\Services\Gateway;

class PaymentGatewayService
{
    /**
     * @var Gateway
     */
    private $gatewayService;

    public function __construct()
    {
        $this->gatewayService = new Gateway();
    }
}
