<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    /**
     * @var WalletService
     */
    public $walletService;

    const NO_WALLET_AVAILABLE_TO_USER = 'There is no wallet available to this user.';
    const OPERATION_ENDED_SUCCESSFULLY = 'The operation ended successfully.';
    const WALLET_ENABLED = 'Wallet was successfully enabled.';

    public function __construct()
    {
        $this->walletService = new WalletService();
        //$this->middleware('auth:api');
    }

    /**
     * Enables wallet for user
     * @param Request $request
     * @return JsonResponse
     */
    public function enable(Request $request, $nickname) {
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
        $wallet = auth()->user()->wallet;
        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|string|max:255',
            'transfer_to' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }


        $this->walletService->transfer($wallet);

        return response()->json(['message' => self::OPERATION_ENDED_SUCCESSFULLY]);
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
    public function balance(Request $request)
    {
        $wallet = $this->walletService->fromRequest($request);

        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        $balance = $this->walletService->getBalance($wallet);

        return response()->json([
            'message' => self::OPERATION_ENDED_SUCCESSFULLY,
            'balance' => $balance
        ]);
    }

    /**
     * Gathers all transaction data upon given period
     * @param Request $request
     */
    public function statement(Request $request)
    {
        $wallet = auth()->user()->wallet;
        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        $period = [
            'start' => $request->get('start'),
            'end' => $request->get('end')
        ];



        $statement = $this->walletService->getStatement($wallet, $period);

        return response()->json([
            'message' => self::OPERATION_ENDED_SUCCESSFULLY,
            'statement' => $statement
        ]);
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
}
