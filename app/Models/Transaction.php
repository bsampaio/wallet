<?php

namespace App\Models;

use Carbon\Carbon;
use Lifepet\Utils\Date;
use Lifepet\Utils\Number;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Transaction
 * @package App\Models
 * @property int $id
 * @property string $order
 * @property int $amount
 * @property string $description
 * @property int $status
 * @property int $type
 * @property string $statusForHumans
 * @property Wallet $from
 * @property Wallet $to
 * @property int $charge_id
 * @property Charge $charge
 * @property int $retries
 * @property Carbon|null $last_retry_at
 * @property Carbon|null $scheduled_to
 * @property Carbon|null $confirmed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Transaction extends Model
{
    use HasFactory;

    protected $dates = ['scheduled_to', 'confirmed_at'];

    protected $hidden = ['from_id', 'to_id', 'id'];

    const STATUS__FAILURE   = 0;
    const STATUS__SUCCESS   = 1;
    const STATUS__SCHEDULED = 2;
    const STATUS__CANCELED  = 3;

    const TYPE__TRANSFER = 1;
    const TYPE__CHARGE   = 2;
    const TYPE__CASHBACK = 3;

    public function from()
    {
        return $this->belongsTo(Wallet::class, 'from_id');
    }

    public function to()
    {
        return $this->belongsTo(Wallet::class, 'to_id');
    }

    public function charge()
    {
        return $this->belongsTo(Charge::class, 'charge_id');
    }

    public function scopeSuccessfull(Builder $query): Builder
    {
        return $query->where('status', '=', self::STATUS__SUCCESS);
    }

    public function scopeSentBy(Builder $query, $wallet): Builder
    {
        return $query->where('from_id', $wallet->id);
    }

    public function scopeReceivedBy(Builder $query, $wallet): Builder
    {
        return $query->where('to_id', $wallet->id);
    }

    public function scopeOwnedBy(Builder $query, $wallet): Builder
    {
        return $query->where(function(Builder $q) use ($wallet) {
             $q->sentBy($wallet)->orWhere(function(Builder $q) use ($wallet) {
                $q->receivedBy($wallet);
             });
        });
    }

    public function scopeBetweenPeriod(Builder $query, $period): Builder
    {
        return $query->whereBetween('confirmed_at', $period);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('id', 'DESC');
    }

    public static function transformForStatement(Wallet $owner): \Closure
    {
        return function($t) use ($owner) {
            if($owner->id == $t->from->id) {
                $t->amount *= -1;
            }

            return Transaction::presenter($t);
        };
    }

    public function getStatusForHumansAttribute()
    {
        return [
            self::STATUS__FAILURE   => __('FAILURE'),
            self::STATUS__SUCCESS   => __('SUCCESS'),
            self::STATUS__CANCELED  => __('CANCELED'),
            self::STATUS__SCHEDULED => __('SCHEDULED'),
        ][$this->status];
    }

    public function getTypeForHumansAttribute()
    {
        return [
            self::TYPE__TRANSFER   => __('TRANSFER'),
            self::TYPE__CHARGE     => __('CHARGE'),
            self::TYPE__CASHBACK   => __('CASHBACK'),
        ][$this->type];
    }

    public function getAmountConvertedToMoneyAttribute()
    {
        return $this->amount / 100;
    }

    public static function presenter(Transaction $t): array
    {
        return [
            'order'          => $t->order,
            'amount'         => $t->amount,
            'formatted'      => Number::money($t->amountConvertedToMoney),
            'description'    => $t->description,
            'status_number'  => $t->status,
            'status'         => $t->statusForHumans,
            'from'           => $t->from->user->nickname,
            'to'             => $t->to->user->nickname,
            'confirmed_at'   => $t->confirmed_at->format(Date::BRAZILIAN_DATETIME),
            'type_number'    => $t->type,
            'type'           => $t->typeForHumans
        ];
    }
}
