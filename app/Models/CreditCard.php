<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CreditCard
 * @package App\Models
 * @property int $id
 * @property string $hash
 * @property bool $main
 * @property bool $active
 * @property string $number
 * @property string $expiration_month
 * @property string $expiration_year
 * @property Wallet $wallet
 * @property int $wallet_id
 * @property string $nickname
 * @property int $external_id
 * @property string $manager
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method static find(int $id)
 */
class CreditCard extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'cards';

    protected $fillable = ['main', 'active'];

    protected $hidden = ['wallet_id', 'manager'];

    const MANAGER__JUNO = 'JUNO';

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function scopeHeldBy(Builder $query, Wallet $wallet): Builder
    {
        return $query->where('wallet_id', $wallet->id);
    }
}
