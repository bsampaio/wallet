<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Charge\AmountTransferedIsDifferentOfCharged;
use App\Exceptions\Charge\ChargeAlreadyExpired;
use App\Exceptions\Charge\ChargeAlreadyPaid;
use App\Exceptions\Charge\IncorrectReceiverOnTransfer;
use App\Exceptions\Charge\InvalidChargeReference;
use App\Exceptions\User\InvalidUserDataReceived;
use App\Exceptions\Wallet\AmountLowerThanMinimum;
use App\Exceptions\Wallet\CantTransferToYourself;
use App\Exceptions\Wallet\NotEnoughtBalance;
use App\Exceptions\Wallet\NoValidReceiverFound;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ChargeService;
use App\Services\UserService;
use App\Services\WalletService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    const ONLY_ACTIVE_WALLETS_CAN_MAKE_OR_RECEIVE_CHARGES = 'Only active wallets can make or receive charges.';
    const THERE_WAS_AN_ERROR_TRYING_TO_MAKE_YOUR_CHARGE = 'There was an error trying to make your charge.';
    const NO_WALLET_AVAILABLE_TO_USER = 'There is no wallet available to this user.';
    const OPERATION_ENDED_SUCCESSFULLY = 'The operation ended successfully.';
    const WALLET_ENABLED = 'Wallet was successfully enabled.';
    const THE_CHARGE_ALREADY_EXPIRED = "The charge already expired.";
    const CHARGE_IS_INVALID_OR_DID_NOT_EXIST = 'Charge is invalid or did not exist.';

    /**
     * @var WalletService
     */
    public $walletService;
    /**
     * @var UserService
     */
    public $userService;
    /**
     * @var ChargeService
     */
    public $chargeService;


    public function __construct()
    {
        $this->walletService = new WalletService();
        $this->userService = new UserService();
        $this->chargeService = new ChargeService();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function make(Request $request): JsonResponse
    {
        try {
            $autoPassword = $request->get('automatic_password', true);

            $user   = $this->userService->makeFromRequest($request, $autoPassword);
            $wallet = $this->walletService->enable($user);

            return response()->json(['message' => self::WALLET_ENABLED, 'wallet_key' => $wallet->wallet_key]);
        } catch (InvalidUserDataReceived $e) {
            return response()->json(['errors'=> $e->getErrors()], 422);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => self::UNEXPECTED_ERROR_OCCURRED], 500);
        }
    }

    /**
     * Enables wallet for user
     * @param Request $request
     * @param $nickname
     * @return JsonResponse
     */
    public function enable(Request $request, $nickname): JsonResponse
    {
        $user = User::nickname($nickname)->first();
        if(!$user) {
            return response()->json(['message' => AuthController::NO_USER_FOUND_WITH_GIVEN_DATA], 401);
        }

        try {
            $wallet = $this->walletService->enable($user);

            if(!$wallet) {
                return response()->json(['message' => self::UNEXPECTED_ERROR_OCCURRED], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => self::UNEXPECTED_ERROR_OCCURRED, 'exception' => $e->getMessage()], 500);
        }

        return response()->json(['message' => self::WALLET_ENABLED, 'wallet_key' => $wallet->wallet_key]);
    }

    /**
     * This method allows to add balance to the wallet account
     * @param Request $request
     */
    public function deposit(Request $request)
    {
        //Validate request

        //Get credit card amount

        //Register payment

        //Add total amount
    }

    /**
     * Allows to transfer available balance to another wallet
     * @param Request $request
     * @return JsonResponse
     */
    public function transfer(Request $request): JsonResponse
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|integer',
            'transfer_to' => 'required|string|max:255|exists:users,nickname',
            'description' => 'sometimes|string',
            'reference' => 'sometimes|string|exists:charges,reference'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        $description = $request->get('description');
        $reference = $request->get('reference');
        $receiverNickname = $request->get('transfer_to');
        $receiver = $this->walletService->fromNickname($receiverNickname);
        $amount = $request->get('amount');

        try {
            $transaction = $this->walletService->transfer($wallet, $receiver, $amount, $description, $reference);
        } catch (AmountLowerThanMinimum | NotEnoughtBalance | ChargeAlreadyExpired | InvalidChargeReference |
                 AmountTransferedIsDifferentOfCharged | ChargeAlreadyPaid | NoValidReceiverFound | IncorrectReceiverOnTransfer |
                 CantTransferToYourself $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => self::UNEXPECTED_ERROR_OCCURRED, 'exception' => $e->getMessage()], 500);
        }

        return response()->json(['message' => self::OPERATION_ENDED_SUCCESSFULLY, 'transaction' => Transaction::presenter($transaction)]);
    }

    /**
     * This method will generate info to charge another wallet.
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function charge(Request $request)
    {
        //Validate request
        $validator = Validator::make($request->all(), [
            'amount'      => 'required|numeric|integer|gt:0',
            'from'     => 'required|string|max:255|exists:users,nickname',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        //Check account status
        $nickname = $request->get('from');
        $from = $this->walletService->fromNickname($nickname);
        $to = $this->walletService->fromRequest($request);
        $amount = $request->get('amount');
        if(!$from->active || !$to->active) {
            return response()->json(['message' => self::ONLY_ACTIVE_WALLETS_CAN_MAKE_OR_RECEIVE_CHARGES], 400);
        }

        //Generate charge info
        try {
            DB::beginTransaction();
            $charge = $this->chargeService->open($from, $to, $amount);
            //Generate QRCode
            $qrcode = $this->chargeService->qrcode($charge);
            //Response
            DB::commit();

            return response()->json(['image' => $qrcode]);
        } catch (\Exception $e) {
            return response()->json(['message' => self::THERE_WAS_AN_ERROR_TRYING_TO_MAKE_YOUR_CHARGE, 'exception' => $e->getMessage()], 400);
        }
    }

    public function loadCharge(Request $request, $to, $amount, $from, $reference): JsonResponse
    {
        $input = [
            'to'        => $to,
            'amount'    => $amount,
            'from'      => $from,
            'reference' => $reference
        ];
        $validator = Validator::make($input, [
            'amount'      => 'required|numeric|integer|gt:0',
            'from'        => 'required|string|max:255|exists:users,nickname',
            'to'          => 'required|string|max:255|exists:users,nickname',
            'reference'   => 'required|string|max:255|exists:charges,reference',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        $charge = $this->chargeService->fromReference($reference, $amount);
        if(!$charge) {
            return response()->json(['message' => self::CHARGE_IS_INVALID_OR_DID_NOT_EXIST], 400);
        }

        if($charge->expired) {
            return response()->json(['message' => self::THE_CHARGE_ALREADY_EXPIRED], 400);
        }

        return response()->json($charge->transformForTransfer());
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function balance(Request $request): JsonResponse
    {
        $wallet = $this->walletService->fromRequest($request);

        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        $balance = $this->walletService->getBalance($wallet);

        return response()->json($balance);
    }

    /**
     * Gathers all transaction data upon given period
     * @param Request $request
     * @return JsonResponse
     */
    public function statement(Request $request): JsonResponse
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        $period = [
            'start' => $request->get('start'),
            'end' => $request->get('end')
        ];

        $statement = $this->walletService->getStatement($wallet, $period);

        return response()->json($statement);
    }

    /**
     * @param Request $request
     * @param $nickname
     * @return array|JsonResponse
     */
    public function key(Request $request, $nickname)
    {
        $user = User::nickname($nickname)->first();
        if(!$user) {
            return response()->json(['message' => AuthController::NO_USER_FOUND_WITH_GIVEN_DATA], 401);
        }
        $wallet = $user->wallet;

        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        if(!$wallet->wallet_key) {
            $this->walletService->ensuredKeyGeneration($wallet);
            $wallet = $user->wallet;
        }

        return [
            'wallet_key' => $wallet->wallet_key
        ];
    }

    /**
     * @return mixed
     */
    public function users()
    {
        return $this->walletService->availableUsers();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function info(Request $request): JsonResponse
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }
        $info = [
            'nickname' => $wallet->user->nickname,
            'email' => $wallet->user->email,
            'available' => $wallet->active,
        ];

        return response()->json($info);
    }
}
