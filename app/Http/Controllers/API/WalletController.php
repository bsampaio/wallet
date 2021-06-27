<?php

namespace App\Http\Controllers\API;

use App\Exceptions\Charge\AmountTransferedIsDifferentOfCharged;
use App\Exceptions\Charge\ChargeAlreadyExpired;
use App\Exceptions\Charge\ChargeAlreadyPaid;
use App\Exceptions\Charge\IncorrectReceiverOnTransfer;
use App\Exceptions\Charge\InvalidChargeReference;
use App\Exceptions\CreditCard\CreditCardAmountShouldBeGreaterOrEqualTotalAmount;
use App\Exceptions\CreditCard\CreditCardUseIsRequired;
use App\Exceptions\CreditCard\InstallmentDoesntReachMinimumValue;
use App\Exceptions\User\InvalidUserDataReceived;
use App\Exceptions\Wallet\AmountLowerThanMinimum;
use App\Exceptions\Wallet\AmountSumIsLowerThanTotalTransfer;
use App\Exceptions\Wallet\CantTransferToYourself;
use App\Exceptions\Wallet\NotEnoughtBalance;
use App\Exceptions\Wallet\NoValidReceiverFound;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Integrations\Juno\Models\Address;
use App\Integrations\Juno\Models\Billing;
use App\Integrations\Juno\Services\Gateway;
use App\Models\Charge;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\ChargeService;
use App\Services\CardTokenizerService;
use App\Services\CreditCardService;
use App\Services\UserService;
use App\Services\WalletService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Lifepet\Utils\Date;

class WalletController extends Controller
{
    const ONLY_ACTIVE_WALLETS_CAN_MAKE_OR_RECEIVE_CHARGES = 'Only active wallets can make or receive charges.';
    const THERE_WAS_AN_ERROR_TRYING_TO_MAKE_YOUR_CHARGE = 'There was an error trying to make your charge.';
    const NO_WALLET_AVAILABLE_TO_USER = 'There is no wallet available to this user.';
    const OPERATION_ENDED_SUCCESSFULLY = 'The operation ended successfully.';
    const WALLET_ENABLED = 'Wallet was successfully enabled.';
    const THE_CHARGE_ALREADY_EXPIRED = "The charge already expired.";
    const CHARGE_IS_INVALID_OR_DID_NOT_EXIST = 'Charge is invalid or did not exist.';
    const BUSINESS_ONLY_FEATURE = 'Feature available only for business wallets';
    const THERE_IS_NOT_CREDIT_CARD_AVAILABLE_FOR_THIS_WALLET = "There is not credit card available for this wallet. Please add one.";

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

    /**
     * @var CreditCardService
     */
    public $creditCardService;


    public function __construct()
    {
        $this->walletService = new WalletService();
        $this->userService = new UserService();
        $this->chargeService = new ChargeService();
        $this->creditCardService = new CreditCardService();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function make(Request $request): JsonResponse
    {
        $type = $request->get('type', Wallet::TYPE__PERSONAL);

        try {
            $autoPassword = $request->get('automatic_password', true);

            $user   = $this->userService->makeFromRequest($request, $autoPassword);
            $wallet = $this->walletService->enable($user, $type);

            return response()->json(['message' => self::WALLET_ENABLED, 'wallet_key' => $wallet->wallet_key]);
        } catch (InvalidUserDataReceived $e) {
            return response()->json(['errors'=> $e->getErrors()], 422);
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
     * @throws Exception
     */
    public function transfer(Request $request): JsonResponse
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        $validator = Validator::make($request->all(), [
            'amount'      => 'required|numeric|integer',
            'transfer_to' => 'required|string|max:255|exists:users,nickname',
            'description' => 'sometimes|string',
            'reference'   => 'sometimes|string|exists:charges,reference',
            'tax'         => 'sometimes|numeric|min:1',
            'cashback'    => 'sometimes|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        $description = $request->get('description');
        $reference = $request->get('reference');
        $receiverNickname = $request->get('transfer_to');
        $receiver = $this->walletService->fromNickname($receiverNickname);
        $amount = $request->get('amount');
        $tax = $request->get('tax');
        $cashback = $request->get('cashback');
        $compensateAfter = $request->get('compensate_after', $receiver->getDefaultCompensationDays());

        try {
            $transaction = $this->walletService->transfer($wallet, $receiver, $amount, $description, $reference, $tax, $cashback);
        } catch (AmountLowerThanMinimum | NotEnoughtBalance | ChargeAlreadyExpired | InvalidChargeReference |
                 AmountTransferedIsDifferentOfCharged | ChargeAlreadyPaid | NoValidReceiverFound | IncorrectReceiverOnTransfer |
                 CantTransferToYourself $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => self::OPERATION_ENDED_SUCCESSFULLY, 'transaction' => Transaction::presenter($transaction)]);
    }

    public function creditCardPayment(Request $request): JsonResponse
    {
        //Retrieve wallet
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        //Create services
        $juno = new Gateway();

        //Validate request
        $validator = Validator::make($request->all(), [
            //Receiver
            'transfer_to'     => 'required|string|exists:users,nickname',

            //Credit Card
            'use_credit_card' => 'required|boolean',
            'card_id'         => 'sometimes|numeric|exists:cards,id',

            //Amount composition
            'amount_to_bill_credit_card' => 'sometimes|numeric|integer|gte:1',
            'amount_to_bill_balance'     => 'sometimes|numeric|integer|gte:1',
            'amount_to_transfer'         => 'required|numeric|gte:1',
            'installments'               => 'required|string|max:255',

            //Charge
            'description'        => 'required|string',
            'due_date'           => 'sometimes|date',

            //Address
            'street'       => 'required|string',
            'number'       => 'required|string',
            'neighborhood' => 'required|string',
            'city'         => 'required|string',
            'state'        => 'required|string',
            'post_code'    => 'required|string',
            'complement'   => 'sometimes|string',

            //Billing
            'name'       => 'required|string',
            'document'   => 'required|string',
            'email'      => 'required|string',
            'phone'      => 'required|string',
            'birth_date' => 'required|date',

            //Options
            'use_balance'       => 'required|boolean',
            'reference'         => 'sometimes|string|exists:charges,reference',
            'tax'               => 'sometimes|numeric|min:0',
            'cashback'          => 'sometimes|numeric|min:0',
            'compensate_after'  => 'sometimes|numeric|integer|min:0'
        ]);

        if($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        //Check receiver
        $transfer_to = $request->get('transfer_to');
        $receiver = $this->walletService->fromNickname($transfer_to);

        try {
            $this->walletService->verifyReceiver($receiver);
        } catch (NoValidReceiverFound $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        $amountToTransfer = $request->get('amount_to_transfer');
        //Check use of current balance
        $useBalance = $request->get('use_balance', 1);
        $balanceAmount = 0;
        if($useBalance) {
            $balanceAmount = $request->get('amount_to_bill_balance', 0);
        }

        if($useBalance) {
            try {
                $this->walletService->verifyBalanceTransfer($wallet, $receiver, $balanceAmount);
            } catch (AmountLowerThanMinimum | CantTransferToYourself | NoValidReceiverFound | NotEnoughtBalance $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }

        //Check Credit Card Info
        $useCreditCard = $request->get('use_credit_card');
        $creditCardAmount = $request->get('amount_to_bill_credit_card', 0);
        $installments = $request->get('installments');
        try {
            $this->creditCardService->verifyCreditCardTransfer($wallet, $receiver, $useBalance, $balanceAmount,  $useCreditCard, $creditCardAmount, $amountToTransfer, $installments);
        } catch (AmountSumIsLowerThanTotalTransfer | CreditCardAmountShouldBeGreaterOrEqualTotalAmount | CreditCardUseIsRequired | InstallmentDoesntReachMinimumValue $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        $creditCard = null;
        if($useCreditCard) {
            $creditCardService = new CreditCardService();
            $cardId = $request->get('card_id');

            if($cardId) {
                $creditCard = $creditCardService->find($wallet, $cardId);
            } else {
                $creditCard = $creditCardService->main($wallet);
            }

            if(!$creditCard) {
                return response()->json(['message' => self::THERE_IS_NOT_CREDIT_CARD_AVAILABLE_FOR_THIS_WALLET], 422);
            }
        }

        //TODO: Create JUNO Charge
        $charge = $this->getCharge($request, $juno);
        $charge->setAsCreditCardPayment();

        $address = $this->getAddress($request, $juno);
        $billing = $this->getBilling($request, $juno, $address);
        try {
            $chargeResponse = $juno->charge($charge, $billing);
        } catch (GuzzleException | Exception $e) {
            $error = "There was a problem while communicating with the payment gateway and trying to process the CHARGE.\n" . $e->getMessage();
            Log::error($error);
            return response()->json(['message' => $error], 500);
        }
        $embedded = $chargeResponse->_embedded;

        $openPayment = $this->chargeService->convertJunoEmbeddedToOpenPayment($wallet, $embedded, Charge::PAYMENT_TYPE__CREDIT_CARD, $charge, $billing, $balanceAmount, $amountToTransfer, $creditCard);

        //TODO: Create JUNO Payment
        try {
            $paymentResponse = $juno->pay($openPayment->external_charge_id, $billing->transformForPayment(), $creditCard->hash);
        } catch (GuzzleException | Exception $e) {
            $error = "There was a problem while communicating with the payment gateway and trying to process the PAYMENT.\n" . $e->getMessage();
            Log::error($error);
            return response()->json(['message' => $error], 500);
        }

        $payment = $this->chargeService->confirmJunoPayment($openPayment, $paymentResponse);

        //TODO: Create Transfer
        try {
            $description = $request->get('description');
            $tax = $request->get('tax');
            $cashback = $request->get('cashback');
            $reference = $request->get('reference');
            $compensateAfter = $request->get('compensate_after', $receiver->getDefaultCompensationDays());

            $transaction = $this->walletService->transferWithPayment($wallet, $receiver, $amountToTransfer, $balanceAmount, $payment, $compensateAfter, $description, $reference, $tax, $cashback);
            return response()->json(['message' => self::OPERATION_ENDED_SUCCESSFULLY, 'transaction' => $transaction->toArray()]);
        } catch (AmountLowerThanMinimum | NotEnoughtBalance | ChargeAlreadyExpired | InvalidChargeReference |
                 AmountTransferedIsDifferentOfCharged | ChargeAlreadyPaid | NoValidReceiverFound | IncorrectReceiverOnTransfer |
                 CantTransferToYourself $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (Exception $e) {
            return response()->json(['message' => self::UNEXPECTED_ERROR_OCCURRED], 400);
        }
    }

    /**
     * This method will generate info to charge another wallet.
     * @param Request $request
     * @return JsonResponse
     */
    public function charge(Request $request): JsonResponse
    {
        //Validate request
        $validator = Validator::make($request->all(), [
            'amount'      => 'required|numeric|integer|gt:0',
            'from'     => 'required|string|max:255|exists:users,nickname',
            'base_url' => 'sometimes|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        //Check account status
        $nickname = $request->get('from');
        $from = $this->walletService->fromNickname($nickname);
        $to = $this->walletService->fromRequest($request);
        $amount = $request->get('amount');
        $base_url = $request->get('base_url');

        if(!$from->active || !$to->active) {
            return response()->json(['message' => self::ONLY_ACTIVE_WALLETS_CAN_MAKE_OR_RECEIVE_CHARGES], 400);
        }

        //Generate charge info
        try {
            DB::beginTransaction();
            $charge = $this->chargeService->open($from, $to, $amount, $base_url);
            //Generate QRCode
            $qrcode = $this->chargeService->qrcode($charge);
            //Response
            DB::commit();

            return response()->json([
                'charge' => $charge->transformForTransfer(),
                'image' => $qrcode
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => self::THERE_WAS_AN_ERROR_TRYING_TO_MAKE_YOUR_CHARGE, 'exception' => $e->getMessage()], 400);
        }
    }

    public function loadCharge(Request $request, $reference, $from = null, $to = null, $amount = null): JsonResponse
    {
        $input = [
            'reference' => $reference
        ];

        foreach(['to', 'amount', 'from'] as $parameter) {
            if($$parameter) {
                $input[$parameter] = $$parameter;
            }
        }

        $validator = Validator::make($input, [
            'reference'   => 'required|string|max:255|exists:charges,reference',
            'amount'      => 'sometimes|required|numeric|integer|gt:0',
            'from'        => 'sometimes|required|string|max:255|exists:users,nickname',
            'to'          => 'sometimes|required|string|max:255|exists:users,nickname',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        $charge = $this->chargeService->fromReference($reference);
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

    public function setDefaultTax(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tax'          => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }
        if(!$wallet->business) {
            return response()->json(['message' => self::BUSINESS_ONLY_FEATURE], 401);
        }

        $wallet->tax = $request->tax;
        $wallet->update();

        return response()->json(['message' => self::OPERATION_ENDED_SUCCESSFULLY], 200);
    }

    public function setDefaultCashback(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cashback'          => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => self::NO_WALLET_AVAILABLE_TO_USER], 401);
        }
        if(!$wallet->business) {
            return response()->json(['message' => self::BUSINESS_ONLY_FEATURE], 401);
        }

        $wallet->cashback = $request->cashback;
        $wallet->update();

        return response()->json(['message' => self::OPERATION_ENDED_SUCCESSFULLY], 200);
    }

    /**
     * @param Request $request
     * @param Gateway $juno
     * @return Address
     */
    private function getAddress(Request $request, Gateway $juno): Address
    {
        $street = $request->get('street');
        $number = $request->get('number');
        $neighborhood = $request->get('neighborhood');
        $city = $request->get('city');
        $state = $request->get('state');
        $postCode = $request->get('post_code');
        $complement = $request->get('complement');

        $address = $juno->buildAddress($street, $number, $neighborhood, $city, $state, $postCode, $complement);
        return $address;
    }

    /**
     * @param Request $request
     * @param Gateway $juno
     * @return \App\Integrations\Juno\Models\Charge
     */
    private function getCharge(Request $request, Gateway $juno): \App\Integrations\Juno\Models\Charge
    {
        $useBalance = $request->get('use_balance', 1);
        $description = $request->get('description');
        $creditCardAmount = $request->get('amount_to_bill_credit_card') / 100;
        $installments = $request->get('installments');
        $dueDate = $request->get('due_date');
        $dueDate = $dueDate ? Carbon::createFromFormat('Y-m-d', $dueDate) : now();

        $charge = $juno->buildCharge($description, $creditCardAmount, $installments, $dueDate);
        return $charge;
    }

    /**
     * @param Request $request
     * @param Gateway $juno
     * @param Address $address
     * @return Billing
     */
    private function getBilling(Request $request, Gateway $juno, Address $address): Billing
    {
        $name = $request->get('name');
        $document = $request->get('document');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $birthDate = Carbon::createFromFormat(Date::UTC_DATE, $request->get('birth_date'));

        $billing = $juno->buildBilling($name, $document, $email, $phone, $birthDate, $address);
        return $billing;
    }
}
