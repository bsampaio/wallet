<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class WebhookService extends Resource {

    public function endpoint(): string
    {
        return 'notifications/webhooks';
    }

    public function register(array $form_params = [])
    {
        return $this->create($form_params);
    }
}
