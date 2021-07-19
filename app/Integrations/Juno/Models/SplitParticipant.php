<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 07/07/2021
 * Time: 16:43
 */

namespace App\Integrations\Juno\Models;


class SplitParticipant extends Model
{
    public $recipientToken;
    public $amount;
    public $amountReminder;
    public $chargeFee;

    /**
     * SplitParticipant constructor.
     * @param $recipientToken
     * @param $amount
     * @param $amountReminder
     * @param $chargeFee
     */
    public function __construct(string $recipientToken, float $amount, bool $amountReminder = false, bool $chargeFee = false)
    {
        $this->recipientToken = $recipientToken;
        $this->amount = $amount;
        $this->amountReminder = $amountReminder;
        $this->chargeFee = $chargeFee;
    }
}