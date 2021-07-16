<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Webhook
 * @package App\Models
 * @property int $id
 * @property string $event
 * @property string $secret
 * @property string $status
 * @property string $url
 * @property int $wallet_id
 * @property Wallet $wallet
 * @method static event(string $event)
 * @method static fromWallet(string $event)
 */
class Webhook extends Model
{
    use HasFactory;

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function scopeFromWallet(Builder $query, Wallet $wallet)
    {
        return $query->where('wallet_id', $wallet->id);
    }

    public function scopeEvent(Builder $query, string $event)
    {
        return $query->where('event', $event);
    }
}
