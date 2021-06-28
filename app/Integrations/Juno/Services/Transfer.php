<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class Transfer extends Resource {

    public function endpoint(): string
    {
        return 'transfers';
    }

    public function createTransfer(array $form_params = [])
    {
        return $this->create($form_params);
    }

}
