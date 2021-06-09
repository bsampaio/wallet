<?php

namespace App\Models;

use App\Utils\Date;
use App\Utils\Number;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use HasFactory;

    protected $dates = ['scheduled_to', 'confirmed_at'];

    const STATUS__FAILURE = 0;
    const STATUS__SUCCESS = 1;
    const STATUS__SCHEDULED = 2;
    const STATUS__CANCELED = 3;

    public function from()
    {
        return $this->belongsTo(Wallet::class, 'from_id');
    }

    public function to()
    {
        return $this->belongsTo(Wallet::class, 'to_id');
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

    public function scopeBetweenPeriod(Builder $query, $period)
    {
        return $query->whereBetween('confirmed_at', $period);
    }

    public function scopeRecent(Builder $query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public static function transformForStatement(Wallet $owner): \Closure
    {
        return function($t) use ($owner) {
            $amount = $t->amount/100;
            if($owner->id == $t->from->id) {
                $amount *= -1;
            }

            return [
                'order'          => $t->order,
                'amount'         => $amount,
                'formatted'      => Number::money($amount),
                'description'    => $t->description,
                'status_number'  => $t->status,
                'status'         => $t->statusForHumans,
                'from'           => $t->from->user->nickname,
                'to'             => $t->to->user->nickname,
                'confirmed_at'   => $t->confirmed_at->format(Date::BRAZILIAN_DATETIME)
            ];
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
}
