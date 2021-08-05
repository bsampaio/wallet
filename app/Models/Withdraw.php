<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}