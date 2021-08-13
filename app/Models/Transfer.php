<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Transfer
 * @package App\Models
 * @property int $id
 * @property int $amount
 * @property int $wallet_id
 * @property Wallet $wallet
 * @property int $transaction_id
 * @property Transaction $transaction
 * @property bool $authorized
 * @property string $authorization_code
 * @property string $external_digital_account_id
 * @property string $external_id
 * @property string $external_status
 * @property Carbon $confirmed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $transfered_at
 * @property Carbon $processed_at
 * @method static Transfer authorized()
 * @method static Transfer unprocessed()
 */
class Transfer extends Model
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

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function scopeAuthorized(Builder $query)
    {
        return $query->where('authorized', false);
    }

    public function scopeUnprocessed(Builder $query)
    {
        return $query->whereNull('processed_at');
    }
}
