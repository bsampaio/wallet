<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property int $from_id
 * @property Wallet $to
 * @property int $to_id
 * @property int $charge_id
 * @property int $origin_id
 * @property Transaction $origin
 * @property Charge $charge
 * @property int $retries
 * @property Carbon|null $last_retry_at
 * @property Carbon|null $scheduled_to
 * @property Carbon|null $confirmed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $balance_amount
 * @property int|null $payment_id
 * @property int payment_amount
 * @property Payment|null $payment
 * @property bool $requires_documentation
 * @property int $documentation_status
 * @property Carbon|null $documentation_sent_at
 * @property bool $waitingCompensation
 * @property Carbon $compensate_at
 * @property Carbon $compensation_authorized_at
 * @property bool $authorized
 * @property float $balanceAmountConvertedToMoney
 * @method static Builder waitingCompensation(Carbon $when = null)
 * @method static Builder byOrder(string $order)
 */
class Transaction extends Model
{
    use HasFactory;

    protected $dates = ['scheduled_to', 'confirmed_at', 'compensate_at'];

    protected $hidden = ['from_id', 'to_id', 'id'];

    const STATUS__FAILURE   = 0;
    const STATUS__SUCCESS   = 1;
    const STATUS__SCHEDULED = 2;
    const STATUS__CANCELED  = 3;
    const STATUS__WAITING   = 4;

    const TYPE__TRANSFER = 1;
    const TYPE__CHARGE   = 2;
    const TYPE__CASHBACK = 3;
    const TYPE__TAX      = 4;

    const DOCUMENTATION_STATUS__PENDING    = 0;
    const DOCUMENTATION_STATUS__SENT       = 1;
    const DOCUMENTATION_STATUS__VERIFYING  = 2;
    const DOCUMENTATION_STATUS__AUTHORIZED = 3;


    public function getAmountConvertedToMoneyAttribute()
    {
        return $this->amount / 100;
    }

    public function getBalanceAmountConvertedToMoneyAttribute()
    {
        return $this->balance_amount / 100;
    }

    public function getStatusForHumansAttribute()
    {
        return [
            self::STATUS__FAILURE   => __('FAILURE'),
            self::STATUS__SUCCESS   => __('SUCCESS'),
            self::STATUS__CANCELED  => __('CANCELED'),
            self::STATUS__SCHEDULED => __('SCHEDULED'),
            self::STATUS__WAITING   => __('WAITING'),
        ][$this->status];
    }

    public function getTypeForHumansAttribute()
    {
        return [
            self::TYPE__TRANSFER   => __('TRANSFER'),
            self::TYPE__CHARGE     => __('CHARGE'),
            self::TYPE__CASHBACK   => __('CASHBACK'),
            self::TYPE__TAX        => __('TAX'),
        ][$this->type];
    }

    public function getWaitingCompensationAttribute(): bool
    {
        return $this->status === self::STATUS__WAITING;
    }

    public function getAuthorizedAttribute(): bool
    {
        return $this->requires_documentation &&
               $this->documentation_status === self::DOCUMENTATION_STATUS__AUTHORIZED &&
               $this->compensation_authorized_at->lte(now());
    }


    public function charge(): BelongsTo
    {
        return $this->belongsTo(Charge::class, 'charge_id');
    }

    public function derived(): HasMany
    {
        return $this->hasMany(Transaction::class, 'origin_id');
    }

    public function from(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'from_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'origin_id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'to_id');
    }


    public function scopeBetweenPeriod(Builder $query, $period): Builder
    {
        return $query->whereBetween('confirmed_at', $period);
    }

    public function scopeOwnedBy(Builder $query, $wallet): Builder
    {
        return $query->where(function(Builder $q) use ($wallet) {
            $q->sentBy($wallet)->orWhere(function(Builder $q) use ($wallet) {
                $q->receivedBy($wallet);
            });
        });
    }

    public function scopeReceivedBy(Builder $query, $wallet): Builder
    {
        return $query->where('to_id', $wallet->id);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('id', 'DESC');
    }

    public function scopeSentBy(Builder $query, $wallet): Builder
    {
        return $query->where('from_id', $wallet->id);
    }

    public function scopeShowable(Builder $query): Builder
    {
        return $query->whereIn('type', [self::TYPE__TRANSFER, self::TYPE__CHARGE]);
    }

    public function scopeSuccessfull(Builder $query): Builder
    {
        return $query->where('status', '=', self::STATUS__SUCCESS);
    }

    public function scopeWaiting(Builder $query): Builder
    {
        return $query->where('status', '=', self::STATUS__WAITING);
    }

    public function scopeConfirmed(Builder $query)
    {
        return $query->where(function(Builder $query) {
            $query->successfull()->orWhere(function(Builder $query) {
                $query->waiting();
            });
        });
    }

    public function scopeWaitingCompensation(Builder $query, Carbon $when = null): Builder
    {
        $query = $query->where('status', '=', self::STATUS__WAITING);
        if($when) {
            $query->whereNotNull('compensate_at')->where('compensate_at', '<=', $when);
        }
        return $query;
    }

    public function scopeAuthorized(Builder $query)
    {
        $query = $query->where(function(Builder $query) {
            $query->where('requires_documentation', 1)
                ->where('documentation_status', 3)
                ->where('compensation_authorized_at', '<=', now())
                ->orWhere(function(Builder $query) {
                    $query->where('requires_documentation', 0);
                });
        });

        return $query;
    }

    public function scopeByOrder(Builder $query, string $order): Builder
    {
        return $query->where('order', $order);
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

    public static function transformDerived(Wallet $owner): \Closure
    {
        return function($t) use ($owner) {
            if($owner->id == $t->from->id) {
                $t->amount *= -1;
            }

            return Transaction::presenter($t);
        };
    }

    public static function presenter(Transaction $t): array
    {
        $orderedDerived = [];
        if($t->derived) {
            $orderedDerived = $t->derived->map(self::transformDerived($t->to));
            $orderedDerived = collect($orderedDerived)->sortByDesc('type_number')->values()->all();
        }

        return [
            'order'          => $t->order,
            'amount'         => $t->amount,
            'balance_amount' => $t->balance_amount,
            'payment_amount' => $t->payment_amount,
            'formatted'      => Number::money($t->amountConvertedToMoney),
            'description'    => $t->description,
            'status_number'  => $t->status,
            'status'         => $t->statusForHumans,
            'from'           => $t->from->transformForTransaction(),
            'to'             => $t->to->transformForTransaction(),
            'origin'         => $t->origin ? $t->origin->order : null,
            'confirmed_at'   => $t->confirmed_at->format(Date::BRAZILIAN_DATETIME),
            'type_number'    => $t->type,
            'type'           => $t->typeForHumans,
            'derived'        => $t->derived ? $orderedDerived : null,
            'payment'        => $t->payment ? $t->payment->transformForTransaction() : null,
        ];
    }

    public function toArray(): array
    {
        return self::presenter($this);
    }

    public function shouldBeCompensated(): bool
    {
        return $this->waitingCompensation &&
               $this->compensate_at->lte(now());
    }
}
