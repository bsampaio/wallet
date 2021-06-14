<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/06/2021
 * Time: 16:10
 */

namespace App\Services;


use App\Exceptions\Charge\AmountTransferedIsDifferentOfCharged;
use App\Exceptions\Charge\ChargeAlreadyExpired;
use App\Exceptions\Charge\ChargeAlreadyPaid;
use App\Exceptions\Charge\IncorrectReceiverOnTransfer;
use App\Exceptions\Charge\InvalidChargeReference;
use App\Exceptions\Wallet\AmountLowerThanMinimum;
use App\Exceptions\Wallet\CantTransferToYourself;
use App\Exceptions\Wallet\NotEnoughtBalance;
use App\Exceptions\Wallet\NoValidReceiverFound;
use App\Models\Charge;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Lifepet\Utils\Number;
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
        $key = config('wallet.headers.Wallet-Key');
        if(!$request->hasHeader($key) || !$request->header($key)) {
            return null;
        }

        $walletKey = $request->header($key);

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
     * @param $reference
     * @throws AmountLowerThanMinimum
     * @throws AmountTransferedIsDifferentOfCharged
     * @throws CantTransferToYourself
     * @throws ChargeAlreadyExpired
     * @throws ChargeAlreadyPaid
     * @throws IncorrectReceiverOnTransfer
     * @throws InvalidChargeReference
     * @throws NoValidReceiverFound
     * @throws NotEnoughtBalance
     */
    public function authorizeTransfer(Wallet $wallet, Wallet $receiver, int $amount, $reference)
    {
        if($wallet->user->id === $receiver->user->id) {
            throw new CantTransferToYourself();
        }

        if($amount < 1) {
            throw new AmountLowerThanMinimum();
        }

        if(($wallet->balanceInCents - $amount) < 0) {
            throw new NotEnoughtBalance();
        }

        if(!$receiver || !$receiver->active) {
            throw new NoValidReceiverFound();
        }

        //Verify charge based transfer.
        if($reference) {
            $this->authorizeChargePayment($reference, $amount, $receiver);
        }
    }

    /**
     * @param Wallet $wallet
     * @param Wallet $receiver
     * @param int $amount Amount to transfer in cents
     * @param null $description
     * @param null $reference
     * @return Transaction
     * @throws AmountLowerThanMinimum
     * @throws AmountTransferedIsDifferentOfCharged
     * @throws ChargeAlreadyExpired
     * @throws ChargeAlreadyPaid
     * @throws InvalidChargeReference
     * @throws NoValidReceiverFound
     * @throws NotEnoughtBalance
     * @throws CantTransferToYourself
     * @throws IncorrectReceiverOnTransfer
     * @throws Exception
     */
    public function transfer(Wallet $wallet, Wallet $receiver, int $amount, $description = null, $reference = null): Transaction
    {
        $this->authorizeTransfer($wallet, $receiver, $amount, $reference);

        return $this->transactionService->transfer($wallet, $receiver, $amount, $description, $reference);
    }

    /**
     * @param $period
     * @return array
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
     * @return bool
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
        } catch (Exception $e) {
           Log::error($e);

           return false;
        }
    }

    /**
     * @param $reference
     * @param int $amount
     * @param $receiver
     * @throws AmountTransferedIsDifferentOfCharged
     * @throws ChargeAlreadyExpired
     * @throws ChargeAlreadyPaid
     * @throws IncorrectReceiverOnTransfer
     * @throws InvalidChargeReference
     */
    private function authorizeChargePayment($reference, int $amount, $receiver): void
    {
        $charge = Charge::reference($reference)->first();
        if (!$charge) {
            throw new InvalidChargeReference();
        }
        if ($charge->paid) {
            throw new ChargeAlreadyPaid();
        }
        if ($charge->expired) {
            throw new ChargeAlreadyExpired();
        }
        if ($amount !== $charge->amount) {
            throw new AmountTransferedIsDifferentOfCharged($amount, $charge->amount);
        }
        if ($charge->to_id !== $receiver->id) {
            throw new IncorrectReceiverOnTransfer($charge->to->user->nickname, $receiver->user->nickname);
        }
    }
}
