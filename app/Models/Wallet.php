<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Wallet
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property double $balance
 * @property bool $active
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
}
