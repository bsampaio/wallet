<?php

namespace App\Models;

use App\Utils\Date;
use App\Utils\Number;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Withdraw
 * @package App\Models
 * @property int $id
 * @property int $wallet_id
 * @property Wallet $wallet
 * @property int $amount
 * @property bool $authorized
 * @property string $authorization_code
 * @property Carbon $authorized_at
 * @property string $external_digital_account_id
 * @property string $external_id
 * @property string $external_status
 * @property Carbon $scheduled_to
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $processed_at
 * @method static authorized()
 * @method static unprocessed()
 */
class Withdraw extends Model
{
    use HasFactory;

    const STATUS__REQUESTED = 'REQUESTED';
    const STATUS__NEEDS_CHECK = 'NEEDS_CHECK';
    const STATUS__CHECK_FAILED = 'CHECK_FAILED';
    const STATUS__AWAITING_EXECUTION = 'AWAITING_EXECUTION';
    const STATUS__EXECUTED = 'EXECUTED';
    const STATUS__REJECTED = 'REJECTED';
    const STATUS__INVALID_BANK_ACCOUNT = 'INVALID_BANK_ACCOUNT';
    const STATUS__CANCELED = 'CANCELED';

    protected $casts = [
        'authorized' => 'bool'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function getAmountConvertedToMoneyAttribute()
    {
        return $this->amount * 100;
    }

    public function scopeAuthorized(Builder $query): Builder
    {
        return $query->where('authorized', 1)->whereNotNull('authorization_code');
    }

    public function scopeUnprocessed(Builder $query): Builder
    {
        return $query->whereNull('processed_at');
    }

    public function transformForRequest()
    {
        return [
            'requester' => $this->wallet->user->nickname,
            'amount' => Number::money($this->amountConvertedToMoney),
            'authorized' => $this->authorized,
            'identifier' => $this->external_id,
            'status' => $this->external_status,
            'authorization_code' => $this->authorization_code,
            'authorized_at' => $this->authorized_at ? $this->authorized_at->format(Date::UTC_DATETIME) : null,
            'scheduled_to' => $this->scheduled_to ? $this->scheduled_to->format(Date::UTC_DATETIME) : null,
            'created_at' => $this->created_at ? $this->created_at->format(Date::UTC_DATETIME) : null,
        ];
    }
}
