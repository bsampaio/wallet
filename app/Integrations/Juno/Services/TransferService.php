<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class TransferService extends Resource {

    public function endpoint(): string
    {
        return 'transfers';
    }

    /**
     * @param array $form_params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createTransfer(array $form_params = [])
    {
        return $this->create($form_params);
    }

}
