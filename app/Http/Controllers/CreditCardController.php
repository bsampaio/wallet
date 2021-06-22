<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\WalletController;
use App\Integrations\Juno\Services\CreditCardService;
use App\Models\CreditCard;
use App\Services\CardTokenizerService;
use App\Services\UserService;
use App\Services\WalletService;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use LVR\CreditCard\CardCvc;
use LVR\CreditCard\CardExpirationMonth;
use LVR\CreditCard\CardExpirationYear;
use LVR\CreditCard\CardNumber;

class CreditCardController extends Controller
{
    const CANT_GENERATE_HASH_TOKEN = "We can't generate a hash token to your card data.";
    /**
     * @var WalletService
     */
    public $walletService;
    /**
     * @var UserService
     */
    public $userService;

    private $cardTokenizerService;

    public function __construct()
    {
        $this->walletService = new WalletService();
        $this->userService = new UserService();
        $this->cardTokenizerService = new CardTokenizerService();
    }

    public function tokenize(Request $request)
    {
        $params = $this->getCardDataFromRequest($request);

        $validator = Validator::make($params, [
            'card_number' => ['required', new CardNumber()],
            'holder_name' => ['required', 'string'],
            'security_code' => ['required', new CardCvc($params['card_number'])],
            'expiration_month' => ['required', new CardExpirationMonth($params['expiration_year'])],
            'expiration_year' => ['required', new CardExpirationYear($params['expiration_month'])]
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        try {
            $hash = $this->cardTokenizerService->tokenize($params['card_number'], $params['holder_name'], $params['security_code'], $params['expiration_month'], $params['expiration_year']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => self::UNEXPECTED_ERROR_OCCURRED]);
        }

        if(!$hash) {
            return response()->json(['errors' => [self::CANT_GENERATE_HASH_TOKEN]], 422);
        }

        return response()->json(['hash' => $hash]);
    }

    public function removeCard()
    {

    }

    public function cards(Request $request): JsonResponse
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => WalletController::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        return response()->json(['cards' => CreditCard::heldBy($wallet)->get()]);
    }

    public function addCard(Request $request): JsonResponse
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => WalletController::NO_WALLET_AVAILABLE_TO_USER], 401);
        }

        //validate
        $params = $this->getCardDataFromRequest($request);

        $validator = $this->getCardValidation($params);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }
        //tokenize
        try {
            $hash = $this->cardTokenizerService->tokenize($params['card_number'], $params['holder_name'], $params['security_code'], $params['expiration_month'], $params['expiration_year']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['errors' => self::UNEXPECTED_ERROR_OCCURRED . " while trying to tokenize your card."]);
        }

        if(!$hash) {
            return response()->json(['errors' => [self::CANT_GENERATE_HASH_TOKEN]], 422);
        }
        //store
        $junoCreditCardService = new CreditCardService();
        try {
            $junoCard = $junoCreditCardService->tokenizeCard([
                'creditCardHash' => $hash
            ]);
        } catch (RequestException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents());
            return response()->json(compact('error'), $error->status);
        }

        $cardNickname = $request->get('card_nickname');
        $creditCard = $this->buildCreditCard($junoCard, $wallet, $cardNickname);
        $creditCard->save();

        return response()->json(['card' => $creditCard]);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getCardDataFromRequest(Request $request): array
    {
        $cardNumber = $request->get('card_number');
        $holderName = $request->get('holder_name');
        $securityCode = $request->get('security_code');
        $expirationMonth = $request->get('expiration_month');
        $expirationYear = $request->get('expiration_year');

        $params = [
            'card_number' => str_replace(' ', '', $cardNumber),
            'holder_name' => $holderName,
            'security_code' => $securityCode,
            'expiration_month' => $expirationMonth,
            'expiration_year' => $expirationYear
        ];
        return $params;
    }

    /**
     * @param array $params
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function getCardValidation(array $params): \Illuminate\Contracts\Validation\Validator
    {
        $validator = Validator::make($params, [
            'card_number' => ['required', new CardNumber()],
            'holder_name' => ['required', 'string'],
            'security_code' => ['required', new CardCvc($params['card_number'])],
            'expiration_month' => ['required', new CardExpirationMonth($params['expiration_year'])],
            'expiration_year' => ['required', new CardExpirationYear($params['expiration_month'])],
            'card_nickname' => ['sometimes', 'string']
        ]);
        return $validator;
    }

    /**
     * @param $junoCard
     * @param \App\Models\Wallet $wallet
     * @param $cardNickname
     * @return CreditCard
     */
    private function buildCreditCard($junoCard, \App\Models\Wallet $wallet, $cardNickname): CreditCard
    {
        $creditCard = new CreditCard();
        $creditCard->hash = $junoCard->creditCardId;
        $creditCard->main = 1;
        $creditCard->active = 1;
        $creditCard->number = $junoCard->last4CardNumber;
        $creditCard->expiration_month = $junoCard->expirationMonth;
        $creditCard->expiration_year = $junoCard->expirationYear;
        $creditCard->wallet_id = $wallet->id;
        $creditCard->manager = CreditCard::MANAGER__JUNO;
        $creditCard->nickname = $cardNickname;
        return $creditCard;
    }
}
