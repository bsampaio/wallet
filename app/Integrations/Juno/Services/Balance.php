<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class Balance extends Resource {

    public function endpoint(): string
    {
        return 'balance';
    }

    public function retrieveBalance()
    {
        return $this->retrieveAll();
    }
}
