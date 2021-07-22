<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @package App\Models
 * @property Wallet $wallet
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method static Builder nickname(string $nickname)
 * @method static Builder email(string $email)
 */
class User extends Authenticatable
{
    const MASTER_NICKNAME = 'lifepet';

    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nickname'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasOne
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * @param Builder $query
     * @param $nickname
     * @return Builder
     */
    public function scopeNickname(Builder $query, $nickname): Builder
    {
        return $query->where('nickname', $nickname);
    }

    public function scopeEmail(Builder $query, $email): Builder
    {
        return $query->where('email', $email);
    }

    public function scopeMaster()
    {
        return $this->nickname(self::MASTER_NICKNAME);
    }
}
