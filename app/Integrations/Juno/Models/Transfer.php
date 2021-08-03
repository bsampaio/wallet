<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/08/2021
 * Time: 18:01
 */

namespace App\Integrations\Juno\Models;


class Transfer
{
    public $id;
    public $digitalAccountId;
    public $creationDate;
    public $transferDate;
    public $amount;
    public $status;
    public $recipient;

    public function __construct(object $data)
    {
        $this->id = $data->id;
        $this->digitalAccountId = $data->digitalAccountId;
        $this->creationDate = $data->creationDate;
        $this->transferDate = $data->transferDate;
        $this->amount = $data->amount;
        $this->status = $data->status;
        $this->recipient = $data->recipient;
    }
}