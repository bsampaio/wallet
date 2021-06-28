<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class Webhook extends Resource {

    public function endpoint(): string
    {
        return 'notifications/webhooks';
    }

    public function create(array $form_params = [])
    {
        return $this->create($form_params);
    }
}
