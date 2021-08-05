<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/08/2021
 * Time: 14:42
 */

namespace App\Integrations\Juno\Models;


class Balance
{
    public $balance;
    public $withheldBalance;
    public $transferableBalance;

    public function __construct(object $data)
    {
        $this->balance = $data->balance;
        $this->withheldBalance = $data->withheldBalance;
        $this->transferableBalance = $data->transferableBalance;
    }
}