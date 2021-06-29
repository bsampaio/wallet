<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Wallet
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property int $type
 * @property User $user
 * @property double $balance
 * @property bool $active
 * @property string $wallet_key
 * @property int $cashback
 * @property int $tax
 */
class Wallet extends Model
{
    const TYPE__PERSONAL = 1;
    const TYPE__BUSINESS = 2;

    const DEFAULT_PERSONAL_COMPENSATION_DAYS = 0;
    const DEFAULT_BUSINESS_COMPENSATION_DAYS = 2;

    use HasFactory;

    public $table = 'wallets';

    public $casts = [
        'active' => 'boolean'
    ];


    public function getBalanceInCentsAttribute()
    {
        return $this->balance * 100;
    }

    public function getBusinessAttribute(): bool
    {
        return $this->type === self::TYPE__BUSINESS;
    }

    public function getPersonalAttribute(): bool
    {
        return $this->type === self::TYPE__PERSONAL;
    }

    public function getTypeForHumansAttribute()
    {
        return [
            self::TYPE__BUSINESS => 'BUSINESS',
            self::TYPE__PERSONAL => 'PERSONAL'
        ][$this->attributes['type']];
    }

    public function cards(): HasMany
    {
        return $this->hasMany(CreditCard::class);
    }

    public function owner(): BelongsTo
    {
        return $this->user();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeLockedBy(Builder $query, $key): Builder
    {
        return $query->where('wallet_key', $key);
    }

    public function getDefaultCompensationDays(): int
    {
        return $this->personal ? self::DEFAULT_PERSONAL_COMPENSATION_DAYS : self::DEFAULT_BUSINESS_COMPENSATION_DAYS;
    }
}
