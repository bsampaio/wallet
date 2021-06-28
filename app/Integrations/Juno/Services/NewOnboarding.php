<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class NewOnboarding extends Resource {

    public function endpoint(): string
    {
        return 'onboarding/link-request';
    }

    public function createOnboardingWhiteLabel(array $form_params = [])
    {
        return $this->create($form_params);
    }
}
