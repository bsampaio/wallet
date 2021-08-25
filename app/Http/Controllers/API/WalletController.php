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
use App\Exceptions\CreditCard\ReceiverDigitalAccountNotEnabled;
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
use App\Integrations\Juno\Services\Pix;
use App\Models\Charge;
use App\Models\DigitalAccount;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Webhook;
use App\Services\ChargeService;
use App\Services\CardTokenizerService;
use App\Services\CreditCardService;
use App\Services\PaymentService;
use App\Services\UserService;
use App\Services\WalletService;
use App\Services\WebhookService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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
    const THERE_IS_NO_USER_WITH_THIS_NICKNAME = 'There is no user with this nickname';

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

    /**
     * @var PaymentService
     */
    public $paymentService;



    public function __construct()
    {
        $this->walletService = new WalletService();
        $this->userService = new UserService();
        $this->chargeService = new ChargeService();
        $this->creditCardService = new CreditCardService();
        $this->paymentService = new PaymentService();
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
     * @return JsonResponse
     */
    public function deposit(Request $request): JsonResponse
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
            //Amount composition
            'amount_to_transfer'         => 'required|numeric|gte:1',

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
            'tax_type'          => 'in:percentage,fixed',
            'tax'               => 'sometimes|numeric|min:0',
            'compensate_after'  => 'sometimes|numeric|integer|min:0'
        ]);

        if($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        //Check receiver
        try {
            $this->walletService->verifyReceiver($wallet);
        } catch (NoValidReceiverFound $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        $amountToTransfer = $request->get('amount_to_transfer');
        $tax = $request->get('tax');
        $taxType = $request->get('tax_type');

        //Check Pix Info
        try {
            $this->paymentService->verifyPixDeposit($wallet, $amountToTransfer, $tax);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

        //TODO: Create JUNO Charge
        $charge = $this->getPixCharge($request, $juno);

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

        $openPayment = $this->chargeService->convertJunoEmbeddedToOpenCreditCardPayment($wallet, $embedded, Charge::PAYMENT_TYPE__CREDIT_CARD, $charge, $billing, $balanceAmount, $amountToTransfer, $creditCard);
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
            'amount'        => 'required|numeric|integer',
            'transfer_to'   => 'required|string|max:255|exists:users,nickname',
            'description'   => 'sometimes|string',
            'reference'     => 'sometimes|string|exists:charges,reference',
            'tax'           => 'sometimes|numeric|min:1',
            'cashback'      => 'sometimes|numeric|min:1'
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
        $balanceAmount = $amount;
        $paymentAmount = 0;

        try {
            $transaction = $this->walletService->transfer($wallet, $receiver, $amount, $balanceAmount, $paymentAmount, $compensateAfter, $description, $reference, $tax, $cashback);
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
            'amount_to_bill_balance'     => 'sometimes|numeric|integer|gte:0',
            'amount_to_transfer'         => 'required|numeric|gte:1',
            'installments'               => 'required|integer|max:255',

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


        $reference = $request->get('reference');

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

        //DENIES USE OF BALANCE
        $useBalance = false;
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
        $useCreditCard = $request->get('use_credit_card', 1);
        $creditCardAmount = $request->get('amount_to_bill_credit_card', 0);


        $installments = $request->get('installments', 1);

        try {
            $this->creditCardService->verifyCreditCardTransfer($wallet, $receiver, $useBalance, $balanceAmount,  $useCreditCard, $creditCardAmount, $amountToTransfer, $installments, $reference);
        } catch (AmountSumIsLowerThanTotalTransfer | CreditCardAmountShouldBeGreaterOrEqualTotalAmount | CreditCardUseIsRequired | InstallmentDoesntReachMinimumValue |
                 ChargeAlreadyExpired | InvalidChargeReference | AmountTransferedIsDifferentOfCharged | ChargeAlreadyPaid | IncorrectReceiverOnTransfer | ReceiverDigitalAccountNotEnabled $e) {
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

        //Create JUNO Charge
        $partnerDigitalAccount = $receiver->digitalAccount;
        $charge = $this->getCreditCardCharge($request, $juno, $partnerDigitalAccount);


        $address = $this->getAddress($request, $juno);
        $billing = $this->getBilling($request, $juno, $address);


        try {
            $chargeResponse = $juno->charge($charge, $billing);

            if(isset($chargeResponse->status) && $chargeResponse->status !== 200) {
                return response()->json([
                    'error' => $chargeResponse->error,
                    'details' => $chargeResponse->details
                ], $chargeResponse->status);
            }
        } catch (GuzzleException | Exception $e) {
            $error = "There was a problem while communicating with the payment gateway and trying to process the CHARGE.\n" . $e->getMessage();
            Log::error($error);
            return response()->json(['message' => $error], 500);
        }
        Log::info('Juno charge was created.', [
            'charge' => $chargeResponse
        ]);

        $embedded = $chargeResponse->_embedded;
        $openPayment = $this->chargeService->convertJunoEmbeddedToOpenCreditCardPayment($wallet, $embedded, Charge::PAYMENT_TYPE__CREDIT_CARD, $charge, $billing, $balanceAmount, $amountToTransfer, $creditCard);

        //Create JUNO Payment
        try {
            $paymentResponse = $juno->pay($openPayment->external_charge_id, $billing->transformForPayment(), $creditCard->hash);
        } catch (BadResponseException $e) {
            $error = "There was a problem while communicating with the payment gateway and trying to process the PAYMENT.\n";

            $contents = $e->getResponse()->getBody()->getContents();
            $contents = json_decode($contents);

            Log::error($error, ['contents' => $contents]);

            return response()->json([
                'message' => $error,
                'contents' => $contents
            ], 500);
        } catch (Exception | GuzzleException $e) {
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
            $compensateAfter = $request->get('compensate_after', $receiver->getDefaultCompensationDays());

            $transaction = $this->walletService->transferWithPayment($wallet, $receiver, $amountToTransfer, $balanceAmount, $payment, $compensateAfter, $description, $reference, $tax, $cashback, $useBalance);
            return response()->json(['message' => self::OPERATION_ENDED_SUCCESSFULLY, 'transaction' => $transaction->toArray()]);
        } catch (AmountLowerThanMinimum | NotEnoughtBalance | ChargeAlreadyExpired | InvalidChargeReference |
                 AmountTransferedIsDifferentOfCharged | ChargeAlreadyPaid | NoValidReceiverFound | IncorrectReceiverOnTransfer |
                 CantTransferToYourself $e) {
            return response()->json(['message' => $e->getMessage(), 'exception' => [
                'trace' => $e->getTrace()
            ]], 400);
        } catch (Exception $e) {
            return response()->json(['message' => self::UNEXPECTED_ERROR_OCCURRED], 400);
        }
    }

    /**
     * Create a charge
     *
     * This method will generate info to charge another wallet.
     * @param Request $request
     * @return JsonResponse
     */
    public function charge(Request $request): JsonResponse
    {
        //Validate request
        $validator = Validator::make($request->all(), [
            'amount'      => 'required|numeric|integer|gt:0',
            'from'     => 'sometimes|string|max:255|exists:users,nickname',
            'base_url' => 'sometimes|url',
            'tax' => 'sometimes|integer|gte:0',
            'cashback' => 'sometimes|integer|gte:0',
            'overwritable' => 'sometimes|boolean',
            'params' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        //Check account status
        $nickname = $request->get('from');
        $from = $nickname ? $this->walletService->fromNickname($nickname) : null;
        $to = $this->walletService->fromRequest($request);
        $amount = $request->get('amount');
        $base_url = $request->get('base_url');
        $overwritable = $request->get('overwritable', 1);
        $tax = $request->get('tax');
        $cashback = $request->get('cashback');
        $params = $request->get('params', []);
        $description = $request->get('description');

        if($from) {
            if(!$from->active) {
                return response()->json(['message' => self::ONLY_ACTIVE_WALLETS_CAN_MAKE_OR_RECEIVE_CHARGES], 400);
            }
        }

        if(!$to->active) {
            return response()->json(['message' => self::ONLY_ACTIVE_WALLETS_CAN_MAKE_OR_RECEIVE_CHARGES], 400);
        }

        //Generate charge info
        try {
            DB::beginTransaction();
            $charge = $this->chargeService->open($to, $amount, $from, $base_url, $overwritable, $tax, $cashback, $description, $params);
            //Generate QRCode
            $qrcode = $this->chargeService->qrcode($charge);
            //Response
            DB::commit();

            $charge = $charge->transformForTransfer();
            $responseData = [
                'charge' => $charge,
                'image' => $qrcode
            ];

            Log::info('A charge was successfully made.', ['charge' => $charge]);

            return response()->json($responseData);
        } catch (Exception $e) {
            Log::error('self::THERE_WAS_AN_ERROR_TRYING_TO_MAKE_YOUR_CHARGE', ['exception' => $e]);

            return response()->json([
                'message' => self::THERE_WAS_AN_ERROR_TRYING_TO_MAKE_YOUR_CHARGE,
                'exception' => [
//                    'trace' => $e->getTrace(),
//                    'file' => $e->getFile(),
//                    'line' => $e->getLine(),
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function loadCharge(Request $request, $from = null, $to = null, $amount = null): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference'   => 'required|string|max:255|exists:charges,reference',
            'amount'      => 'sometimes|required|numeric|integer|gt:0',
            'from'        => 'sometimes|required|string|max:255|exists:users,nickname',
            'to'          => 'sometimes|required|string|max:255|exists:users,nickname',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }
        $reference = $request->get('reference');
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
     * Account balance
     *
     * Retrieves total balance available on wallet.
     *
     * @group Wallet
     *
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
     * Statement
     *
     * Gathers all transaction data upon given period
     *
     * @queryParam start string Start of period on 'Y-m-d' date format. Example: 2021-01-01
     * @queryParam end string End of period on 'Y-m-d' date format. Example: 2021-01-31
     * @group Wallet
     *
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
     * Get Wallet-Key
     *
     * Gets the Wallet-Key to grant access to execute transactions.
     *
     * @group Wallet
     * @urlParam nickname string Nickname of user. Example: partner
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
     * List all available users
     *
     * Gets all users registered and available.
     *
     * @group Users
     * @return mixed
     */
    public function users()
    {
        return $this->walletService->availableUsers();
    }

    /**
     * Wallet info
     *
     * Gets wallet information of given Wallet-Key
     *
     * @group Wallet
     *
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
            'digitalAccount' => $wallet->digitalAccount ? [
                'account_number' => $wallet->digitalAccount->external_account_number,
                'status' => $wallet->digitalAccount->external_status,
                'document' => $wallet->digitalAccount->external_document,
            ] : null
        ];

        return response()->json($info);
    }

    /**
     * Set default tax for Wallet
     *
     * Defines a default tax percentage on payments.
     *
     * @queryParam tax integer required Amount of tax set as default to be used on transaction payments. Example: 10
     *
     * @group Wallet
     *
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * Set default cashback for Wallet
     *
     * Defines a default cashback percentage on customer payments.
     *
     * @queryParam cashback integer required Amount of cashback returned by default on customer to partner transaction payments. Example: 10
     *
     * @group Wallet
     *
     * @param Request $request
     * @return JsonResponse
     */
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
     * Find user by nickname
     *
     * Finds user with a given nickname
     *
     * @queryParam nickname string required Nickname of user. Example: partner
     *
     * @group Users
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function userByNickname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname'  => 'required',
        ]);

        $validator->validate();

        /**
         * @var User $user
         */
        $user = User::nickname($request->get("nickname"))->first();

        if(!$user) {
            return response()->json(['message' => self::THERE_IS_NO_USER_WITH_THIS_NICKNAME], 404);
        }

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'nickname' => $user->nickname,
            'type'  => $user->wallet->typeForHumans
        ]);
    }

    /**
     * Paginated user search
     *
     * Gets a paginated user list filtering by a given term.
     * The term is used to compare:
     * - Nickname
     * - Email
     * - Name
     *
     * @queryParam page integer required Which page to show. Example: 1
     * @queryParam term string Term to compare Example: lifepet
     * @group Users
     *
     * @param Request $request
     * @return mixed
     */
    public function paginatedUserSearch(Request $request)
    {
        $page = $request->get('page', 1);
        $amount = 10;
        $term = $request->get('term');
        $type = $request->get('type');


        $query = Wallet::active()->listed();

        if($term) {
            $term = '%' . $term . '%';
            $query->with('User');
            $query->whereHas('user', function($query) use ($term) {
               $query->where('name', 'LIKE', $term)
                     ->orWhere('nickname', 'LIKE', $term)
                     ->orWhere('email', 'LIKE', $term);
            });
        }

        if($type) {
            $query->where('type', $type);
        }

        return $query->forPage($page, $amount)->get()->map(function(Wallet $w) {
            return [
                'name' => $w->user->name,
                'email' => $w->user->email,
                'nickname' => $w->user->nickname,
                'type'  => $w->typeForHumans
            ];
        });
    }

    /**
     * All transactions by period
     *
     *
     * @queryParam start date required Start date. Example: 2021-01-01
     * @queryParam end date required End date. Example: 2021-01-31
     * @queryParam page int Pagination page. Example: 1
     * @queryParam amount int Pagination max amount. Example: 10
     * @group Transactions
     *
     * @param Request $request
     * @return mixed
     */
    public function transactions(Request $request)
    {
        $page = $request->get('page', 1);
        $amount = $request->get('amount', 20);
        list($start, $end) = $this->getDateInterval($request);

        $query = Transaction::query()->whereBetween('created_at', [$start, $end]);
        $count = (clone $query)->count();

        $query->forPage($page, $amount);

        $transactions = $query->get();

        return response()->json([
            'page' => $page,
            'pageResults' => count($transactions),
            'total' => $count,
            'transactions' => $transactions->map(function ($t) {
                return Transaction::presenter($t);
            })
        ]);
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
     * @param DigitalAccount $partnerDigitalAccount
     * @return \App\Integrations\Juno\Models\Charge
     */
    private function getCreditCardCharge(Request $request, Gateway $juno, DigitalAccount $partnerDigitalAccount): \App\Integrations\Juno\Models\Charge
    {
        $description = $request->get('description');
        $amountToTransfer = $request->get('amount_to_transfer') / 100;
        $creditCardAmount = $request->get('amount_to_bill_credit_card') / 100;
        $installments = $request->get('installments');
        $dueDate = $request->get('due_date');
        $dueDate = $dueDate ? Carbon::createFromFormat('Y-m-d', $dueDate) : now();

        $charge = $juno->buildCharge($description, $creditCardAmount, $amountToTransfer, $partnerDigitalAccount, $installments, $dueDate);
        $charge->setAsCreditCardPayment();

        return $charge;
    }

    private function getPixCharge(Request $request, Gateway $juno, DigitalAccount $partnerDigitalAccount): \App\Integrations\Juno\Models\Charge
    {
        $description = $request->get('description');
        $installments = 1;
        $dueDate = $request->get('due_date');
        $dueDate = $dueDate ? Carbon::createFromFormat('Y-m-d', $dueDate) : now();
        $amountToBill = $request->get('amount_to_transfer');
        $taxType = $request->get('tax_type', \App\Integrations\Juno\Models\Charge::TAX_TYPE__FIXED);
        $tax = $request->get('tax', 0);

        if(strtoupper($taxType) === \App\Integrations\Juno\Models\Charge::TAX_TYPE__PERCENTAGE) {
            $amountToBill = $amountToBill * (1 + ($tax / 100));
        } else {
            $amountToBill = $amountToBill + $tax;
        }
        $amountToBill = $amountToBill / 100;

        return $juno->buildCharge($description, $amountToBill, $amountToBill, $partnerDigitalAccount, $installments, $dueDate, [], true);
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

    public function paymentNotification(Request $request)
    {
        $service = new WebhookService();
        $response = $service->read($request, 'PAYMENT_NOTIFICATION');

        return response()->json(['message' => 'Webhook was read successfully']);
    }

    public function chargeStatusChanged(Request $request)
    {
        $service = new WebhookService();
        $response = $service->read($request, 'CHARGE_STATUS_CHANGED');

        return response()->json(['message' => 'Webhook was read successfully']);
    }

    public function setupWebhooks()
    {
        $service = new WebhookService();
        $webhooks = [
            [
                'event' => 'PAYMENT_NOTIFICATION',
                'url' => route('notifications.juno.payment.notification')
            ],
            [
                'event' => 'CHARGE_STATUS_CHANGED',
                'url' => route('notifications.juno.chargeStatusChanged')
            ]
        ];
        foreach(['PAYMENT_NOTIFICATION', 'CHARGE_STATUS_CHANGED'] as $event) {
            if(!Webhook::event($event)->active()->exists()) {
                $service->register($event, route('notifications.juno.payment.notification'));
            }
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getDateInterval(Request $request): array
    {
        $start = $request->get('start');
        $end = $request->get('end');

        if (!$start) {
            $start = now()->startOfMonth();
        } else {
            $start = Carbon::createFromFormat('Y-m-d', $start);
        }

        if (!$end) {
            $end = now()->endOfMonth();
        } else {
            $end = Carbon::createFromFormat('Y-m-d', $end);
        }
        return array($start, $end);
    }
}
