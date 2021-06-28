<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class PicPay extends Resource {

    public function endpoint(): string
    {
        return 'qrcode';
    }

    public function createQRCode(array $form_params = [])
    {
        return $this->create($form_params);
    }

    public function cancelQRCode($id, array $form_params = [])
    {
        return $this->post($id, 'cancel', $form_params);
    }
}
