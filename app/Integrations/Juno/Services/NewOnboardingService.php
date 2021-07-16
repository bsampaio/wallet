<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class NewOnboardingService extends Resource {

    public function __construct($resourceToken, array $args = [])
    {
        parent::__construct($args, $resourceToken);
    }

    public function endpoint(): string
    {
        return 'onboarding/link-request';
    }

    public function createOnboardingWhiteLabel(array $form_params = [])
    {
        return $this->create($form_params);
    }
}
