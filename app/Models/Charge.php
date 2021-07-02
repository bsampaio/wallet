<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lifepet\Utils\Date;
use Lifepet\Utils\Number;

/**
 * Class Charge
 * @package App\Models
 * @property int $id
 * @property string $reference
 * @property string $from_id
 * @property string $to_id
 * @property Wallet $from
 * @property Wallet $to
 * @property Carbon $expires_at
 * @property bool $expired
 * @property int $amount
 * @property int $status
 * @property int $transaction_id
 * @property string $url
 * @property int $tax
 * @property int $cashback
 * @property bool $overwritable
 * @property Transaction $transaction
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Charge extends Model
{
    const STATUS__CANCELED = 0;
    const STATUS__OPEN = 1;
    const STATUS__PAID = 2;

    const PAYMENT_TYPE__CREDIT_CARD = 1;
    const PAYMENT_TYPE__PIX = 2;

    protected $dates = [
        'expires_at', 'created_at', 'updated_at'
    ];
    protected $casts = [
        'overwritable' => 'boolean'
    ];

    use HasFactory;

    public function getExpiredAttribute(): bool
    {
        return now()->gt($this->expires_at);
    }

    public function getPaidAttribute(): bool
    {
        return $this->status === self::STATUS__PAID && !is_null($this->transaction_id);
    }

    public function getStatusForHumansAttribute()
    {
        return [
            self::STATUS__CANCELED  => __('CANCELED'),
            self::STATUS__OPEN      => __('OPEN'),
            self::STATUS__PAID      => __('PAID'),
        ][$this->status];
    }

    /**
     * @return BelongsTo
     */
    public function from(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * @return BelongsTo
     */
    public function to(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function scopeAmount($query, $amount)
    {
        return $query->where('amount', $amount);
    }

    public function scopeReference($query, $reference)
    {
        return $query->where('reference', $reference);
    }

    public function transformForTransfer(): array
    {
        return [
            'to'             => $this->to->user->nickname,
            'amount'         => $this->amount,
            'from'           => $this->from ? $this->from->user->nickname : null,
            'reference'      => $this->reference,
            'description'    => $this->description,
            'status'         => $this->statusForHumans,
            'transaction'    => $this->transaction ? $this->transaction->order : null,
            'expires_at'     => $this->expires_at->format("Y-m-d H:i:s"),
            'formatted'      => Number::money($this->amount / 100),
            'url'            => $this->url,
            'cashback'       => $this->cashback,
            'tax'            => $this->tax,
            'overwritable'   => $this->overwritable
        ];
    }
}
