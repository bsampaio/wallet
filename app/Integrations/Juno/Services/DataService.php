<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class DataService extends Resource
{
    public function endpoint(): string
    {
        return 'data';
    }

    public function getBanks()
    {
        return $this->get('banks');
    }

    public function getCompanyTypes()
    {
        return $this->get('company-types');
    }

    public function getBusinessAreas()
    {
        return $this->get('business-areas');
    }
}
