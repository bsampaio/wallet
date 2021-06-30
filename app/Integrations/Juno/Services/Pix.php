<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class Pix extends Resource {

    public function endpoint(): string
    {
        return 'pix';
    }

    public function createRandomKey($id = null, $action = null, array $form_params = [])
    {
        return $this->post($id,'keys', array_merge([
            'type' => "RANDOM_KEY"
        ], $form_params));
    }

    public function createStaticQRCode($id = null, $action = null, array $form_params = [])
    {
        return $this->post($id,'qrcodes/static', $form_params);
    }


}
