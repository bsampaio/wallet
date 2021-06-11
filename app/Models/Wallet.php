<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Wallet
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property User $user
 * @property double $balance
 * @property bool $active
 * @property string $wallet_key
 */
class Wallet extends Model
{
    use HasFactory;

    public $table = 'wallets';

    public $casts = [
        'active' => 'boolean'
    ];

    public function owner() {
        return $this->user();
    }

    public function user() {
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

    public function getBalanceInCentsAttribute()
    {
        return $this->balance * 100;
    }
}
