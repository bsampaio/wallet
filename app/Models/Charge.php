<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @property Transaction $transaction
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Charge extends Model
{
    const STATUS__CANCELED = 0;
    const STATUS__OPEN = 1;
    const STATUS__PAID = 2;

    use HasFactory;

    public function getUrlAttribute()
    {
        return route('charge.load', [
            'reference' => $this->reference,
            'from'      => $this->from->user->nickname,
            'to'        => $this->to->user->nickname,
            'amount'    => $this->amount
        ]);
    }

    public function getExpiredAttribute(): bool
    {
        return now()->gt($this->expires_at);
    }

    public function scopeReference($query, $reference)
    {
        return $query->where('reference', $reference);
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
}
