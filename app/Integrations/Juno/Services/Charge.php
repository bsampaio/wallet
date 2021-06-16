<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class Charge extends Resource {

    public function endpoint(): string
    {
        return 'charges';
    }

    public function createCharge(array $form_params = [])
    {
        return $this->create($form_params);
    }
}
