<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payment
 * @package App\Models
 * @property int $id
 * @property int $payment_type
 * @property int $amount
 * @property int $original_amount
 * @property int $installments
 * @property float $amount_installments
 * @property float $external_fee
 * @property int $status
 * @property string $manager
 * @property string $street
 * @property string $number
 * @property string $complement
 * @property string $neighborhood
 * @property string $city
 * @property string $state
 * @property string $post_code
 * @property string $external_transaction_id
 * @property string $external_charge_id
 * @property string $external_payment_id
 * @property string $external_checkout_url
 * @property Carbon $external_release_date
 * @property string $fail_reason
 * @property Carbon $paid_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $wallet_id
 * @property Wallet $wallet
 * @property int $card_id
 * @property CreditCard $card
 * @property bool $paid
 */
class Payment extends Model
{
    use HasFactory;

    const STATUS__FAIL = -1;
    const STATUS__OPEN = 0;
    const STATUS_CONFIRMED = 1;

    const PAYMENT_TYPE__CREDIT_CARD = 1;
    const PAYMENT_TYPE__PIX = 2;
    const PAYMENT_TYPE__INVOICE = 3;

    public function getPaidAttribute(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function getStatusForHumansAttribute(): string
    {
        return [
            self::STATUS__FAIL => 'FAIL',
            self::STATUS__OPEN => 'OPEN',
            self::STATUS_CONFIRMED => 'CONFIRMED',
        ][$this->attributes['status']];
    }

    public function getPaymentTypeForHumansAttribute(): string
    {
        return [
            self::PAYMENT_TYPE__CREDIT_CARD => 'CREDIT CARD',
            self::PAYMENT_TYPE__PIX => 'PIX',
            self::PAYMENT_TYPE__INVOICE => 'INVOICE',
        ][$this->attributes['payment_type']];
    }

    public function transformForTransaction(): array
    {
        return [
            'id'  => $this->id,
            'external_transaction_id' => $this->external_transaction_id,
            'amount' => $this->amount,
            'payment_type' => $this->paymentTypeForHumans,
            'payment_type_number' => $this->payment_type,
            'status' => $this->statusForHumans,
            'status_number' => $this->status,
        ];
    }
}
