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

    public function convert(Wallet $wallet): \App\Models\Transfer
    {
        $transfer = new \App\Models\Transfer();
        $transfer->amount = $this->amount;
        $transfer->wallet_id = $wallet->id;
        $transfer->authorization_code = null;
        $transfer->external_digital_account_id = $this->digitalAccountId;
        $transfer->external_id = $this->id;
        $transfer->external_status = $this->status;
        $transfer->transfer_at = $this->transferDate;

        return $transfer;
    }
}