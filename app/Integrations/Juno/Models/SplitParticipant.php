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
    public $amountRemainder;
    public $chargeFee;

    /**
     * SplitParticipant constructor.
     * @param $recipientToken
     * @param $amount
     * @param $amountRemainder
     * @param $chargeFee
     */
    public function __construct(string $recipientToken, float $amount, bool $amountRemainder = false, bool $chargeFee = false)
    {
        $this->recipientToken = $recipientToken;
        $this->amount = $amount;
        $this->amountRemainder = $amountRemainder;
        $this->chargeFee = $chargeFee;
    }
}