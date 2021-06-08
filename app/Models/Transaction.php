<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class Transaction extends Model
{
    use HasFactory;

    protected $dates = ['scheduled_to', 'confirmed_at'];

    const STATUS__FAILURE = 0;
    const STATUS__SUCCESS = 1;
    const STATUS__SCHEDULED = 2;
    const STATUS__CANCELED = 3;

    const TYPE__SEND = -1;
    const TYPE__RECEIVE = 1;


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
}
