<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/06/2021
 * Time: 16:10
 */

namespace App\Services;


use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Utils\Number;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use \Illuminate\Http\Request;

class WalletService
{
    public function __construct()
    {

    }

    /**
     * @param User $user
     * @return Wallet|null
     */
    public function enable(User $user): ?Wallet
    {
        $wallet = $user->wallet;
        if(!$wallet) {
            $wallet = new Wallet();
            $wallet->user_id = $user->id;
            if(!$wallet->save()) {
                return null;
            }
        }

        if(!$wallet->wallet_key) {
            $this->ensuredKeyGeneration($wallet);
        }

        return $user->wallet;
    }

    public function getBalance($wallet)
    {
        $balance = 0;
        if($wallet) {
            $balance = $wallet->balance;
        }

        return [
            'money' => Number::money($balance),
            'value' => $balance
        ];
    }

    public function getStatement($wallet, $period)
    {
        $parsedPeriod = $this->parsePeriod($period);

        $query = Transaction::query();
        $transactions = $query->successfull()->ownedBy($wallet)->betweenPeriod($period)->recent()->get();
    }

    /**
     * @param $period
     */
    public function parsePeriod($period): array
    {
        if (!isset($period['start']) || !$period['start']) {
            $start = now();
        } else {
            $start = Carbon::parse($period['start']);
            if (!$start) {
                $start = now();
            }
        }

        if (!isset($period['end']) || !$period['end']) {
            $end = now()->subDays(30);
        } else {
            $end = Carbon::parse($period['end']);
            if (!$end) {
                $end = now()->subDays(30);
            }
        }

        return [$start, $end];
    }

    /**
     * @param Wallet $wallet
     */
    private function generateWalletKey(Wallet $wallet): bool
    {
        try {
            $randomKey = bin2hex(random_bytes(20));
            $keybase = join('|', [
                $wallet->user->nickname,
                $wallet->user->email,
                $wallet->user->id,
                $wallet->created_at->format('Ymdhis'),
                $randomKey
            ]);
            $key = hash("sha256", $keybase);
            $wallet->wallet_key = $key;
            $wallet->save();

            return true;
        } catch (\Exception $e) {
           Log::error($e);

           return false;
        }
    }

    /**
     * @param Wallet $wallet
     */
    public function ensuredKeyGeneration(Wallet $wallet): void
    {
        do {
            $generated = $this->generateWalletKey($wallet);
        } while (!$generated);
    }

    public function fromRequest(Request $request): ?Wallet
    {
        if(!$request->hasHeader('wallet_key') || !$request->header('wallet_key')) {
            return null;
        }

        $walletKey = $request->header('wallet_key');

        return Wallet::active()->lockedBy($walletKey)->first();
    }
}
