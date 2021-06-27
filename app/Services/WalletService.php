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
use App\Models\Payment;
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
     * @param int $type
     * @return Wallet|null
     */
    public function enable(User $user, int $type): ?Wallet
    {
        $wallet = $user->wallet;
        if(!$wallet) {
            $wallet = new Wallet();
            $wallet->user_id = $user->id;
            $wallet->type = $type;
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
        $transactions = $query->successfull()->showable()->ownedBy($wallet)->betweenPeriod($parsedPeriod)->recent()->get();

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
    public function authorizeTransfer(Wallet $wallet, Wallet $receiver, int $amount, $reference, Payment $payment = null)
    {
        $this->verifySelfTransfer($wallet, $receiver);

        //Verify charge based transfer.
        if($reference) {
            $this->authorizeChargePayment($reference, $amount, $receiver, $payment);
        }

        $this->verifyMinimumTransferAmount($amount);

        $this->verifyAvailableBalance($wallet, $amount);

        $this->verifyReceiver($receiver);
    }

    /**
     * @throws CantTransferToYourself
     * @throws AmountLowerThanMinimum
     * @throws NotEnoughtBalance
     * @throws NoValidReceiverFound
     */
    public function verifyBalanceTransfer(Wallet $wallet, Wallet $receiver, int $amount)
    {
        $this->verifySelfTransfer($wallet, $receiver);

        $this->verifyMinimumTransferAmount($amount);

        $this->verifyAvailableBalance($wallet, $amount);

        $this->verifyReceiver($receiver);
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
    public function transfer(Wallet $wallet, Wallet $receiver, int $amount, $description = null, $reference = null, $tax = null, $cashback = null): Transaction
    {
        $this->authorizeTransfer($wallet, $receiver, $amount, $reference);

        return $this->transactionService->transfer($wallet, $receiver, $amount, $description, $reference, $tax, $cashback);
    }

    /**
     * @throws ChargeAlreadyPaid
     * @throws AmountLowerThanMinimum
     * @throws CantTransferToYourself
     * @throws InvalidChargeReference
     * @throws AmountTransferedIsDifferentOfCharged
     * @throws IncorrectReceiverOnTransfer
     * @throws ChargeAlreadyExpired
     * @throws NotEnoughtBalance
     * @throws NoValidReceiverFound
     * @throws Exception
     */
    public function transferWithPayment(Wallet $wallet, Wallet $receiver, int $amountToTransfer, int $balanceAmount, Payment $payment, int $compensateAfter, $description = null, $reference = null, $tax = null, $cashback = null): Transaction
    {
        $this->authorizeTransfer($wallet, $receiver, $balanceAmount, $reference, $payment);

        return $this->transactionService->transferWithPayment($wallet, $receiver, $amountToTransfer, $balanceAmount, $payment, $compensateAfter, $description, $reference, $tax, $cashback);
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
    private function authorizeChargePayment($reference, int $amount, $receiver, Payment $payment = null): void
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

        if($payment && $payment->paid){
            $amountWithPayment = $amount + $payment->amount;
            if ($amountWithPayment !== $charge->amount) {
                throw new AmountTransferedIsDifferentOfCharged($amountWithPayment, $charge->amount);
            }
        } else  {
            if ($amount !== $charge->amount) {
                throw new AmountTransferedIsDifferentOfCharged($amount, $charge->amount);
            }
        }

        if ($charge->to_id !== $receiver->id) {
            throw new IncorrectReceiverOnTransfer($receiver->user->nickname, $charge->to->user->nickname);
        }
    }

    /**
     * @param Wallet $wallet
     * @param int $amount
     * @throws NotEnoughtBalance
     */
    public function verifyAvailableBalance(Wallet $wallet, int $amount): void
    {
        if (($wallet->balanceInCents - $amount) < 0) {
            throw new NotEnoughtBalance();
        }
    }

    /**
     * @param int $amount
     * @throws AmountLowerThanMinimum
     */
    public function verifyMinimumTransferAmount(int $amount): void
    {
        if ($amount < 1) {
            throw new AmountLowerThanMinimum();
        }
    }

    /**
     * @param Wallet $receiver
     * @throws NoValidReceiverFound
     */
    public function verifyReceiver(Wallet $receiver): void
    {
        if (!$receiver || !$receiver->active) {
            throw new NoValidReceiverFound();
        }
    }

    /**
     * @param Wallet $wallet
     * @param Wallet $receiver
     * @throws CantTransferToYourself
     */
    public function verifySelfTransfer(Wallet $wallet, Wallet $receiver): void
    {
        if ($wallet->user->id === $receiver->user->id) {
            throw new CantTransferToYourself();
        }
    }

}
