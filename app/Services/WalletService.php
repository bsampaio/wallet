<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/06/2021
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\Wallet\AmountLowerThanMinimum;
use App\Exceptions\Wallet\NotEnoughtBalance;
use App\Exceptions\Wallet\NoValidReceiverFound;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Utils\Number;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use \Illuminate\Http\Request;

class WalletService
{

    protected $transactionService;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
    }

    /**
     * Creates a Wallet for the given user.
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

        return Wallet::find($wallet->id);
    }

    /**
     * Gets the total balance inside a given Wallet
     * @param $wallet
     * @return array
     */
    public function getBalance($wallet): array
    {
        $balance = 0;
        if($wallet) {
            $balance = $wallet->balance;
        }

        return [
            'formatted' => Number::money($balance),
            'numeric' => $balance,
            'cents' => $wallet->balanceInCents
        ];
    }

    /**
     * Gets all successfull transactions in a given period.
     * @param $wallet
     * @param $period
     * @return array
     */
    public function getStatement($wallet, $period): array
    {
        $parsedPeriod = $this->parsePeriod($period);

        $query = Transaction::query();
        $transactions = $query->successfull()->ownedBy($wallet)->betweenPeriod($parsedPeriod)->recent()->get();

        return [
            'transactions' => $transactions->map(Transaction::transformForStatement($wallet)),
            'period' => $parsedPeriod
        ];
    }

    /**
     * Grants generation of a wallet key
     * @param Wallet $wallet
     */
    public function ensuredKeyGeneration(Wallet $wallet): void
    {
        do {
            $generated = $this->generateWalletKey($wallet);
        } while (!$generated);
    }

    /**
     * @param Request $request
     * @return Wallet|null
     */
    public function fromRequest(Request $request): ?Wallet
    {
        if(!$request->hasHeader('wallet_key') || !$request->header('wallet_key')) {
            return null;
        }

        $walletKey = $request->header('wallet_key');

        return Wallet::active()->lockedBy($walletKey)->first();
    }

    /**
     * @param string $nickname
     * @return Wallet|null
     */
    public function fromNickname(string $nickname): ?Wallet
    {
        $user = User::nickname($nickname)->first();
        if(!$user) {
            return null;
        }

        return $user->wallet()->first();
    }

    /**
     * @return mixed
     */
    public function availableUsers()
    {
        return Wallet::active()->get()->map(function($w) {
            return $w->user->nickname;
        });
    }

    /**
     * @param Wallet $wallet
     * @param Wallet $receiver
     * @param int $amount Amount to transfer in cents
     * @throws AmountLowerThanMinimum
     * @throws NoValidReceiverFound
     * @throws NotEnoughtBalance
     */
    public function authorizeTransfer(Wallet $wallet, Wallet $receiver, int $amount)
    {
        if($amount < 1) {
            throw new AmountLowerThanMinimum();
        }

        if(($wallet->balanceInCents - $amount) < 0) {
            throw new NotEnoughtBalance();
        }

        if(!$receiver || !$receiver->active) {
            throw new NoValidReceiverFound();
        }
    }

    /**
     * @param Wallet $wallet
     * @param Wallet $receiver
     * @param int $amount Amount to transfer in cents
     * @throws AmountLowerThanMinimum
     * @throws NoValidReceiverFound
     * @throws NotEnoughtBalance
     */
    public function transfer(Wallet $wallet, Wallet $receiver, int $amount)
    {
        $this->authorizeTransfer($wallet, $receiver, $amount);

        $this->transactionService->transfer($wallet, $receiver, $amount);
    }

    /**
     * @param $period
     */
    private function parsePeriod($period): array
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

        return [$end, $start];
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
}
