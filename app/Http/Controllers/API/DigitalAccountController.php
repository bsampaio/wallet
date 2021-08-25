<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyDigitalAccountOpeningRequest;
use App\Integrations\Juno\Models\Balance;
use App\Integrations\Juno\Services\BalanceService;
use App\Integrations\Juno\Services\DataService;
use App\Integrations\Juno\Services\DocumentService;
use App\Integrations\Juno\Services\NewOnboardingService;
use App\Integrations\Juno\Services\WebhookService;
use App\Models\DigitalAccount;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Webhook;
use App\Models\Withdraw;
use App\Services\DigitalAccountService;
use App\Services\Notification\PartnerNotificationService;
use App\Services\TransactionService;
use App\Services\TransferService;
use App\Services\WalletService;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psy\Util\Json;

class DigitalAccountController extends Controller
{
    const THERE_WAS_AN_ERROR_TRYING_TO_OPEN_YOUR_ACCOUNT_ON_JUNO = 'There was an error trying to open your account on Juno.';
    const YOU_CAN_T_ACCESS_THIS_RESOURCE_WITHOUT_AN_OPEN_DIGITAL_ACCOUNT = 'You can\'t access this resource without an open Digital Account.';
    const CAN_T_CREATE_YOUR_DOCUMENT_UPLOAD_LINK = 'Can\'t create your document upload link.';
    const WE_CAN_T_FIND_YOUR_DOCUMENT_LIST = 'We can\'t find your document list.';
    const WE_CAN_T_RETRIEVE_THE_DIGITAL_ACCOUNT_BALANCE_TRY_AGAIN_LATER = 'We can\'t retrieve the Digital Account balance. Try again later.';
    const THE_TOTAL_AMOUNT_REQUESTED_FOR_TRANSFER_IS_GREATER_THAN_THE_TOTAL_AVAILABLE = "The total amount requested for transfer is greater than the total available.";
    private $digitalAccountService = null;
    private $walletService = null;


    const YOUR_ACCOUNT_WAS_SUCCESSFULLY_OPENED_PROCEED_WITH_THE_DOCUMENTS_UPLOAD = 'Your account was successfully opened. Proceed with the documents upload.';

    public function __construct()
    {
        $this->digitalAccountService = new DigitalAccountService();
        $this->walletService = new WalletService();
    }

    public function index(Request $request)
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => WalletController::NO_WALLET_AVAILABLE_TO_USER], 401);
        }
        if(!$wallet->business) {
            return response()->json(['message' => WalletController::BUSINESS_ONLY_FEATURE], 401);
        }

        if(!$wallet->digitalAccount) {
            return response()->json(['message' => 'There is no open digital account']);
        }

        return response()->json(['digital_account' => DigitalAccount::presenter($wallet->digitalAccount)]);
    }

    /**
     * Proceeds with digital account opening.
     * @param CompanyDigitalAccountOpeningRequest $request
     * @return JsonResponse
     */
    public function open(CompanyDigitalAccountOpeningRequest $request): JsonResponse
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => WalletController::NO_WALLET_AVAILABLE_TO_USER], 401);
        }
        if(!$wallet->business) {
            return response()->json(['message' => WalletController::BUSINESS_ONLY_FEATURE], 401);
        }

        $digitalAccount = $this->digitalAccountService->createOpenAccountFromRequest($request, $wallet);
        $companyMembers = $this->digitalAccountService->getCompanyMembersFromRequest($request);
        //Convert to Juno Object Request
        $junoDigitalAccountService = new \App\Integrations\Juno\Services\DigitalAccountService();

        $junoResponse = $junoDigitalAccountService->createDigitalAccount($digitalAccount, $companyMembers);
        if(isset($junoResponse->error) && $junoResponse->status != 200) {
            return response()->json(['message' => '' . self::THERE_WAS_AN_ERROR_TRYING_TO_OPEN_YOUR_ACCOUNT_ON_JUNO . '', 'error' => $junoResponse], 500);
        }

        $digitalAccount = $this->digitalAccountService->appendJunoAdditionalData($digitalAccount, $junoResponse);

        $legalRepresentative = clone $digitalAccount->legalRepresentative;
        unset($digitalAccount->legalRepresentative);
        $digitalAccount->save();
        $legalRepresentative->digital_account_id = $digitalAccount->id;
        $legalRepresentative->save();

        //Register webhooks
        $webhooksService = new WebhookService([], $digitalAccount->external_resource_token);
        $webhookResponse = $webhooksService->register([
            'url' => route('notifications.juno.digital-accounts.changed', ['nickname' => $wallet->user->nickname]),
            'eventTypes' => [
                'DIGITAL_ACCOUNT_STATUS_CHANGED',
            ]
        ]);

        if(isset($webhookResponse->secret)) {
            $webhook = new Webhook();
            $webhook->wallet_id = $wallet->id;
            $webhook->event = 'DIGITAL_ACCOUNT_STATUS_CHANGED';
            $webhook->status = 'ACTIVE';
            $webhook->secret = $webhookResponse->secret;
            $webhook->url = $webhookResponse->url;
            $webhook->save();
        }


        return response()->json(['message' => self::YOUR_ACCOUNT_WAS_SUCCESSFULLY_OPENED_PROCEED_WITH_THE_DOCUMENTS_UPLOAD]);
    }

    public function documentsLink(Request $request)
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => WalletController::NO_WALLET_AVAILABLE_TO_USER], 401);
        }
        if(!$wallet->business) {
            return response()->json(['message' => WalletController::BUSINESS_ONLY_FEATURE], 401);
        }

        if(!$wallet->digitalAccount || !$wallet->digitalAccount->external_resource_token) {
            return response()->json(['message' => self::YOU_CAN_T_ACCESS_THIS_RESOURCE_WITHOUT_AN_OPEN_DIGITAL_ACCOUNT], 400);
        }

        $returnUrl = $request->input('return_url');
        $refreshUrl = $request->input('refresh_url');

        $onboardingService = new NewOnboardingService($wallet->digitalAccount->external_resource_token);
        $whiteLabelOnboarding = $onboardingService->createOnboardingWhiteLabel([
            'type' => 'DOCUMENTS_UPLOAD',
            'emailOptOut' => true,
            'returnUrl' => $returnUrl ?: 'https://www.lifepet.com.br/wallet',
            'refreshUrl' => $refreshUrl ?: 'https://www.lifepet.com.br/wallet'
        ]);
        if(!isset($whiteLabelOnboarding->token)) {
            return response()->json(['message' => self::CAN_T_CREATE_YOUR_DOCUMENT_UPLOAD_LINK, 'error' => $whiteLabelOnboarding], 400);
        }

        return response()->json(['link' => $whiteLabelOnboarding->url]);
    }

    public function listDocuments(Request $request)
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => WalletController::NO_WALLET_AVAILABLE_TO_USER], 401);
        }
        if(!$wallet->business) {
            return response()->json(['message' => WalletController::BUSINESS_ONLY_FEATURE], 401);
        }

        if(!$wallet->digitalAccount || !$wallet->digitalAccount->external_resource_token) {
            return response()->json(['message' => self::YOU_CAN_T_ACCESS_THIS_RESOURCE_WITHOUT_AN_OPEN_DIGITAL_ACCOUNT], 400);
        }

        $documentService = new DocumentService($wallet->digitalAccount->external_resource_token);
        $documents = $documentService->list();

        if(!isset($documents->_embedded)) {
            return response()->json(['message' => self::WE_CAN_T_FIND_YOUR_DOCUMENT_LIST], 400);
        }

        return response()->json(['documents' => $documents->_embedded->documents]);
    }


    public function businessAreas(): JsonResponse
    {
        $businessAreas = Cache::remember('juno|business-areas', 60 * 60, function() {
            $junoDataService = new DataService();
            $response = $junoDataService->getBusinessAreas();
            if($response && $response->_embedded) {
                return $response->_embedded->businessAreas;
            }

            return [];
        });

        if($businessAreas) {
            return response()->json($businessAreas);
        }

        return response()->json(['message' => 'Can\'t get business areas from Juno service.']);
    }

    public function banks(): JsonResponse
    {
        $banks = Cache::remember('juno|banks', 60 * 60, function() {
            $junoDataService = new DataService();
            $response = $junoDataService->getBanks();
            if($response && $response->_embedded) {
                return $response->_embedded->banks;
            }

            return [];
        });

        if($banks) {
            return response()->json($banks);
        }

        return response()->json(['message' => 'Can\'t get banks from Juno service.']);
    }

    public function companyTypes(): JsonResponse
    {
        $companyTypes = Cache::remember('juno|company-types', 60 * 60, function() {
            $junoDataService = new DataService();
            $response = $junoDataService->getCompanyTypes();
            if($response) {
                return $response->companyTypes;
            }

            return [];
        });

        if($companyTypes) {
            return response()->json($companyTypes);
        }

        return response()->json(['message' => 'Can\'t get company types from Juno service.']);
    }

    public function digitalAccountStatusChanged(Request $request, $nickname): JsonResponse
    {
        $user = User::nickname($nickname)->first();
        if(!$user) {
            Log::error('digitalAccountStatusChanged() - User not found:', ['request' => $request->all()]);
        }

        $wallet = $user->wallet;
        if(!$wallet) {
            Log::error('digitalAccountStatusChanged() - Wallet not able to user:', ['request' => $request->all()]);
        }

        Log::info('notifications.juno.DIGITAL_ACCOUNT_STATUS_CHANGED', ['request' => $request->all()]);

        $webhook = Webhook::fromWallet($wallet)->event('DIGITAL_ACCOUNT_STATUS_CHANGED')->first();
        if(!$webhook) {
            Log::error('digitalAccountStatusChanged() - Webhook not registered locally:', ['request' => $request->all()]);
        }

        $payload = file_get_contents('php://input');
        $signature = hash_hmac('sha256', $payload, $webhook->secret);

        if($signature !== $request->headers->get('X-Signature')) {
            //Do all the stuff
            Log::error('digitalAccountStatusChanged() - Signature did not match:', ['request' => $request->all(), 'payload' => $payload]);
            abort(400, 'Can\'t assure payload reliability');
        }

        $data = $request->input('data');
        if(!$data) {
            Log::error('digitalAccountStatusChanged() - Failed: There is no data informed on request.');
            abort('400', 'There is no data informed on request.');
        }

        foreach($data as $changed) {
            $externalId = $changed['entityId'];
            if(!$externalId) {
                Log::error('digitalAccountStatusChanged() - Failed: There is no external_id informed on request.');
                continue;
            }

            $digitalAccount = DigitalAccount::where('external_id', $externalId)->first();
            if(!$digitalAccount) {
                Log::error('digitalAccountStatusChanged() - Failed: No DigitalAccount can be found with the given entityId.', ['entityId' => $externalId]);
                continue;
            }

            $digitalAccount->external_status = $changed['attributes']['status'];
            $digitalAccount->update();
            $id = $digitalAccount->id;
            $previous = $changed['attributes']['previousStatus'];
            $current = $changed['attributes']['status'];
            Log::info("digitalAccountStatusChanged() - Success: DigitalAccount #$id of @$nickname status changed from $previous to $current");

            try {
                $partnerNotificationService = new PartnerNotificationService();
                $partnerNotificationService->digitalAccountStatusChanged($wallet, $digitalAccount->external_status);
            } catch (GuzzleException $e) {
                Log::warning('digitalAccountStatusChanged() - Can\'t notify partner system about the status change.');
            }
        }

        return response()->json(['message' => 'DigitalAccount status successfully updated.']);
    }

    public function detailedBalance(Request $request): JsonResponse
    {
        /**
         * @var Wallet $wallet
         */
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => WalletController::NO_WALLET_AVAILABLE_TO_USER], 401);
        }
        if(!$wallet->business) {
            return response()->json(['message' => WalletController::BUSINESS_ONLY_FEATURE], 401);
        }

        if($wallet->hasValidOpenAccount) {
            return response()->json(['message' => self::YOU_CAN_T_ACCESS_THIS_RESOURCE_WITHOUT_AN_OPEN_DIGITAL_ACCOUNT], 400);
        }

        $resourceToken = $wallet->digitalAccount->external_resource_token;
        if(!$resourceToken) {
            return response()->json(['message' => 'A Digital Account access token can\'t be found.'], 401);
        }

        $junoBalance = $this->getJunoBalance($resourceToken);
        $transactionService = new TransactionService();
        $awaitingDocumentation = $transactionService->getAwaitingDocumentationTotalBalance($wallet);

        return response()->json([
            'juno' => $junoBalance,
            'wallet' => [
                'totalBalance' => round($wallet->balance,2),
                'awaitingDocumentation' => $awaitingDocumentation
            ]
        ]);
    }

    /**
     * Requests an withdraw. Amount in cents.
     * @param Request $request
     * @return JsonResponse
     */
    public function withdraw(Request $request): JsonResponse
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => WalletController::NO_WALLET_AVAILABLE_TO_USER], 401);
        }
        if(!$wallet->business) {
            return response()->json(['message' => WalletController::BUSINESS_ONLY_FEATURE], 401);
        }

        if($wallet->hasValidOpenAccount) {
            return response()->json(['message' => self::YOU_CAN_T_ACCESS_THIS_RESOURCE_WITHOUT_AN_OPEN_DIGITAL_ACCOUNT], 400);
        }

        $resourceToken = $wallet->digitalAccount->external_resource_token;
        if(!$resourceToken) {
            return response()->json(['message' => 'A Digital Account access token can\'t be found.'], 401);
        }

        $request->validate([
            'amount' => 'numeric|gte:1000',
        ], $request->all());

        $amount = $request->get('amount');
        $moneyAmount = $amount / 100;

        $junoBalance = $this->getJunoBalance($resourceToken);
        if(!$junoBalance) {
            return response()->json(['message' => 'We can\'t retrieve your Digital Account balance. Try again later.'], 503);
        }

        $balance = new Balance($junoBalance);
        if($moneyAmount > $balance->transferableBalance) {
            return response()->json(['message' => "The total amount requested for transfer is greater than the total available. ($moneyAmount}) > ($balance->transferableBalance)"], 400);
        }

        $transferService = new TransferService();

        $url = route('notifications.juno.transferStatusChanged', ['nickname' => $wallet->user->nickname]);

        $withdraw = $transferService->openWithdraw($wallet, $amount, $url);
        if(!$withdraw) {
            return response()->json(['message' => "We can't request your withdraw."], 400);
        }

        return response()->json(['message' => "Withdraw successfully opened.", 'withdraw' => $withdraw->transformForRequest()]);
    }

    public function p2pTransfer(Request $request): JsonResponse
    {
        $request->validate([
            'order' => 'required|string|exists:transactions,order'
        ], $request->all());
        //Obter transação
        $order = $request->get('order');
        /**
         * @var Transaction $transaction
         */
        $transaction = Transaction::byOrder($order)->first();
        //Verificar se a transação já foi paga anteriormente
        if($transaction->authorized) {
            return response()->json('The transaction was already authorized previously.', 400);
        }
        //Verificar conta digital
        if(!$transaction->to->hasValidOpenAccount) {
            return response()->json(['message' => self::YOU_CAN_T_ACCESS_THIS_RESOURCE_WITHOUT_AN_OPEN_DIGITAL_ACCOUNT], 400);
        }

        $resourceToken = $transaction->to->digitalAccount->external_resource_token;
        if(!$resourceToken) {
            return response()->json(['message' => "A Digital Account access token can\'t be found on receiver @{$transaction->to->user->nickname}."], 401);
        }

        //Verificar valor de transferência
        $junoBalance = $this->getLifepetBalance();
        if(!$junoBalance) {
            return response()->json(['message' => self::WE_CAN_T_RETRIEVE_THE_DIGITAL_ACCOUNT_BALANCE_TRY_AGAIN_LATER], 503);
        }

        $balance = new Balance($junoBalance);
        if($transaction->balanceAmountConvertedToMoney > $balance->transferableBalance) {
            return response()->json(['message' => self::THE_TOTAL_AMOUNT_REQUESTED_FOR_TRANSFER_IS_GREATER_THAN_THE_TOTAL_AVAILABLE], 400);
        }

        $transferService = new TransferService();

        $url = route('notifications.juno.p2pTransferStatusChanged', ['nickname' => $transaction->to->user->nickname]);
        //Criar solicitação de transferẽncia atrelada à transação
        $transfer = $transferService->openP2pTransfer($transaction, $url);
        //Status de transferência
        if(!$transfer) {
            return response()->json(['message' => 'The transfer can\'t be made. There was an unknown error.'], 500);
        }
        //Notificar
        //Retornar código de transferência
        return response()->json([
            'message' => 'The transfer was sucessfully requested.',
            'transfer' => [
                'identifier' => $transfer->external_id,
                'status' => $transfer->external_status
            ]
        ]);
    }

    /**
     * @param $resourceToken
     * @return mixed
     */
    private function getJunoBalance($resourceToken): ?\stdClass
    {
        $balanceService = new BalanceService([], $resourceToken);
        return $balanceService->retrieveBalance();
    }

    private function getLifepetBalance()
    {
        $balanceService = new BalanceService([], env('JUNO__PRIVATE_TOKEN'));
        return $balanceService->retrieveBalance();
    }

    public function p2pTransferStatusChanged(Request $request, string $nickname): JsonResponse
    {
        $event = 'P2P_TRANSFER_STATUS_CHANGED';
        $logIdentifier = 'notifications.juno.' . $event;
        Log::info($logIdentifier, ['request' => $request->all()]);

        $user = User::nickname($nickname)->first();
        if(!$user) {
            Log::error($logIdentifier . ' - User not found:', ['request' => $request->all()]);
        }

        $wallet = $user->wallet;
        if(!$wallet) {
            Log::error($logIdentifier . ' - Wallet not able to user:', ['request' => $request->all()]);
        }

        $data = $request->input('data');
        if(!$data) {
            Log::error($logIdentifier . ' - Failed: There is no data informed on request.');
            abort('400', 'There is no data informed on request.');
        }

        $webhook = Webhook::fromWallet($wallet)->event('TRANSFER_STATUS_CHANGED')->first();
        if(!$webhook) {
            Log::error($logIdentifier . ' - Webhook not registered locally:', ['request' => $request->all()]);
            abort(400, 'Webhook not found.');
        }

        $payload = file_get_contents('php://input');
        $signature = hash_hmac('sha256', $payload, $webhook->secret);

        if($signature !== $request->headers->get('X-Signature')) {
            Log::error($logIdentifier . ' - Signature did not match:', ['request' => $request->all(), 'payload' => $payload]);
            abort(400, 'Can\'t assure payload reliability');
        }

        $errors = [];
        foreach($data as $changed) {
            $transferExternalId = $changed['entityId'];
            if(!$transferExternalId) {
                $errors[] = $error = 'There is no external_id informed on request.';
                Log::error($logIdentifier . ' - Failed: ' . $error);
                continue;
            }
            /**
             * @var Transfer $transfer
             */
            $transfer = Transfer::where('external_id', $transferExternalId)->first();
            if(!$transfer) {
                $errors[] = $error = 'No Transfer can be found with the given entityId.';
                Log::error($logIdentifier . ' - Failed: ' . $error, ['entityId' => $transferExternalId]);
                continue;
            }

            $transfer->external_status = $changed['attributes']['status'];
            $transfer->transfered_at = $changed['attributes']['transferDate'];
            $transfer->authorized = $transfer->external_status === Transfer::STATUS__EXECUTED;

            $service = new TransferService();
            $transfer = $service->authorizeTransfer($transfer);

            $transfer->update();

            $id = $transfer->id;
            $previous = $changed['attributes']['previousStatus'];
            $current = $changed['attributes']['status'];
            Log::info($logIdentifier . " - Success: Transfer {$transfer->id} from DigitalAccount #$id of $nickname Wallet status changed from $previous to $current");

            try {
                $partnerNotificationService = new PartnerNotificationService();
                $partnerNotificationService->transferStatusChanged($wallet, $transfer, $current);
            } catch (GuzzleException $e) {
                Log::warning($logIdentifier . ' - Can\'t notify partner system about the status change.');
            }
        }

        if(!empty($errors)) {
            return response()->json([
                'message' => 'There are one or more errors during the process of the notification: ',
                'errors' => $errors
            ]);
        }

        return response()->json(['message' => 'DigitalAccount Transfer status successfully updated.']);
    }

    public function transferStatusChanged(Request $request, string $nickname): JsonResponse
    {
        $event = 'TRANSFER_STATUS_CHANGED';
        $logIdentifier = 'notifications.juno.' . $event;
        Log::info($logIdentifier, ['request' => $request->all()]);

        $user = User::nickname($nickname)->first();
        if(!$user) {
            Log::error($logIdentifier . ' - User not found:', ['request' => $request->all()]);
        }

        $wallet = $user->wallet;
        if(!$wallet) {
            Log::error($logIdentifier . ' - Wallet not able to user:', ['request' => $request->all()]);
        }

        $data = $request->input('data');
        if(!$data) {
            Log::error($logIdentifier . ' - Failed: There is no data informed on request.');
            abort('400', 'There is no data informed on request.');
        }

        $webhook = Webhook::fromWallet($wallet)->event($event)->first();
        if(!$webhook) {
            Log::error($logIdentifier . ' - Webhook not registered locally:', ['request' => $request->all()]);
            abort(400, 'Webhook not found.');
        }

        $payload = file_get_contents('php://input');
        $signature = hash_hmac('sha256', $payload, $webhook->secret);

        if($signature !== $request->headers->get('X-Signature')) {
            Log::error($logIdentifier . ' - Signature did not match:', ['request' => $request->all(), 'payload' => $payload]);
            abort(400, 'Can\'t assure payload reliability');
        }

        $errors = [];
        foreach($data as $changed) {
            $transferExternalId = $changed['entityId'];
            if(!$transferExternalId) {
                $errors[] = $error = 'There is no external_id informed on request.';
                Log::error($logIdentifier . ' - Failed: ' . $error);
                continue;
            }

            $withdraw = Withdraw::where('external_id', $transferExternalId)->first();
            if(!$withdraw) {
                $errors[] = $error = 'No Withdraw can be found with the given entityId.';
                Log::error($logIdentifier . ' - Failed: ' . $error, ['entityId' => $transferExternalId]);
                continue;
            }

            $withdraw->external_status = $changed['attributes']['status'];
            //$withdraw->transfered_at = $changed['attributes']['transferDate'];
            $withdraw->authorized = $withdraw->external_status === Withdraw::STATUS__EXECUTED;

            $service = new TransferService();
            $withdraw = $service->authorizeWithdraw($withdraw);

            $withdraw->update();

            $id = $withdraw->id;
            $previous = $changed['attributes']['previousStatus'];
            $current = $changed['attributes']['status'];
            Log::info($logIdentifier . " - Success: Withdraw {$withdraw->id} from DigitalAccount #$id of $nickname Wallet status changed from $previous to $current");

            try {
                $partnerNotificationService = new PartnerNotificationService();
                $partnerNotificationService->withdrawStatusChanged($wallet, $withdraw, $current);
            } catch (GuzzleException $e) {
                Log::warning($logIdentifier . ' - Can\'t notify partner system about the status change.');
            }
        }

        if(!empty($errors)) {
            return response()->json([
                'message' => 'There are one or more errors during the process of the notification: ',
                'errors' => $errors
            ]);
        }

        return response()->json(['message' => 'DigitalAccount Withdraw status successfully updated.']);
    }
}
