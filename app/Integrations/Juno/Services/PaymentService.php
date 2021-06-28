<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;
use GuzzleHttp\Exception\GuzzleException;

class PaymentService extends Resource {

    public function endpoint(): string
    {
        return 'payments';
    }

    /**
     * @throws GuzzleException
     */
    public function createPayment(array $form_params = [])
    {
        return $this->create($form_params);
    }

    /**
     * @throws GuzzleException
     */
    public function capture($id = null, $action = null, array $form_params = [])
    {
        return $this->post($id, 'capture', $form_params);
    }

    /**
     * @throws GuzzleException
     */
    public function refunds($id = null, $action = null, array $form_params = [])
    {
        return $this->post($id, 'refunds', $form_params);
    }
}
