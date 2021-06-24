<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;
use GuzzleHttp\Exception\GuzzleException;

class CreditCardService extends Resource {

    public function endpoint(): string
    {
        return 'credit-cards/tokenization';
    }

    /**
     * @throws GuzzleException
     */
    public function tokenizeCard(array $form_params = [])
    {
        return $this->create($form_params);
    }
}
