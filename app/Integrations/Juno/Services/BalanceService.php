<?php

namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;

class BalanceService extends Resource {

    public function endpoint(): string
    {
        return 'balance';
    }

    public function retrieveBalance()
    {
        return $this->all();
    }
}
