<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;
use GuzzleHttp\Exception\GuzzleException;

class ChargeService extends Resource {

    public function endpoint(): string
    {
        return 'charges';
    }

    /**
     * @throws GuzzleException
     */
    public function createCharge(array $form_params = [])
    {
        return $this->create($form_params);
    }
}
