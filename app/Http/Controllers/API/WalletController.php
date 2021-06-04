<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
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
        $this->middleware('auth:api');
    }

    /**
     * Enables wallet for user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function enable(Request $request) {
        $user = auth()->user();

        try {
            $wallet = $this->walletService->enable($user);

            if(!$wallet) {
                return response()->json(['message' => self::UNEXPECTED_ERROR_OCCURRED], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => self::UNEXPECTED_ERROR_OCCURRED, 'exception' => $e->getMessage()], 500);
        }

        return response()->json(['message' => self::WALLET_ENABLED]);
    }

    /**
     * This method allows to add balance to the wallet account
     * @param Request $request
     */
    public function deposit(Request $request)
    {

    }

    /**
     * Allows to transfer available balance to another wallet
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transfer(Request $request)
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
            return response(['errors'=>$validator->errors()->all()], 422);
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

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function balance(Request $request)
    {
        $wallet = auth()->user()->wallet;
        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        $balance = $this->walletService->getBalance($wallet);

        return response()->json([
            'message' => self::OPERATION_ENDED_SUCCESSFULLY,
            'balance' => $balance
        ]);
    }
}
