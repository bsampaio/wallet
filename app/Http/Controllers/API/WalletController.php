<?php

namespace App\Http\Controllers\API;

use App\Exceptions\User\InvalidUserDataReceived;
use App\Exceptions\Wallet\AmountLowerThanMinimum;
use App\Exceptions\Wallet\NotEnoughtBalance;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    /**
     * @var WalletService
     */
    public $walletService;
    /**
     * @var UserService
     */
    public $userService;

    const NO_WALLET_AVAILABLE_TO_USER = 'There is no wallet available to this user.';
    const OPERATION_ENDED_SUCCESSFULLY = 'The operation ended successfully.';
    const WALLET_ENABLED = 'Wallet was successfully enabled.';

    public function __construct()
    {
        $this->walletService = new WalletService();
        $this->userService = new UserService();
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        $receiverNickname = $request->get('transfer_to');
        $receiver = $this->walletService->fromNickname($receiverNickname);
        $amount = $request->get('amount');

        try {
            $transaction = $this->walletService->transfer($wallet, $receiver, $amount);
        } catch (AmountLowerThanMinimum | NotEnoughtBalance $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => self::UNEXPECTED_ERROR_OCCURRED, 'exception' => $e->getMessage()], 500);
        }

        return response()->json(['message' => self::OPERATION_ENDED_SUCCESSFULLY, 'transaction' => $transaction]);
    }

    /**
     * This method will generate info to charge another wallet.
     * @param Request $request
     */
    public function charge(Request $request)
    {
        //Validate request

        //Check account status

        //Generate charge info

        //Generate QRCode

        //Response
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

    public function users()
    {
        return $this->walletService->availableUsers();
    }

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
