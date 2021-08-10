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
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Webhook;
use App\Models\Withdraw;
use App\Services\DigitalAccountService;
use App\Services\Notification\PartnerNotificationService;
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
            return response()->json(['message' => 'There was an error trying to open your account on Juno.', 'error' => $junoResponse], 500);
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

    public function inspect()
    {

    }

    public function requestTransfer()
    {

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
            return response()->json(['message' => 'You can\'t access this resource without an open Digital Account.'], 400);
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
            return response()->json(['message' => 'Can\'t create your document upload link.', 'error' => $whiteLabelOnboarding], 400);
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
            return response()->json(['message' => 'You can\'t access this resource without an open Digital Account.'], 400);
        }

        $documentService = new DocumentService($wallet->digitalAccount->external_resource_token);
        $documents = $documentService->list();

        if(!isset($documents->_embedded)) {
            return response()->json(['message' => 'We can\'t find your document list.'], 400);
        }

        return response()->json(['documents' => $documents->_embedded->documents]);
    }

    public function info()
    {
        
    }

    public function businessAreas()
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

    public function banks()
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

    public function companyTypes() {
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

    public function digitalAccountStatusChanged(Request $request, $nickname)
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

    public function detailedBalance(Request $request)
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => WalletController::NO_WALLET_AVAILABLE_TO_USER], 401);
        }
        if(!$wallet->business) {
            return response()->json(['message' => WalletController::BUSINESS_ONLY_FEATURE], 401);
        }

        if($wallet->hasValidOpenAccount) {
            return response()->json(['message' => 'You can\'t access this resource without an open Digital Account.'], 400);
        }

        $resourceToken = $wallet->digitalAccount->external_resource_token;
        if(!$resourceToken) {
            return response()->json(['message' => 'A Digital Account access token can\'t be found.'], 401);
        }

        $junoBalance = $this->getJunoBalance($resourceToken);

        return response()->json([
            'juno' => $junoBalance,
            'wallet' => [
                'totalBalance' => $wallet->balance,
                'awaitingDocumentation' => $wallet->balance - $junoBalance->balance
            ]
        ]);
    }

    /**
     * Requests an withdraw. Amount in cents.
     * @param Request $request
     * @return JsonResponse
     */
    public function withdraw(Request $request)
    {
        $wallet = $this->walletService->fromRequest($request);
        if(!$wallet) {
            return response()->json(['message' => WalletController::NO_WALLET_AVAILABLE_TO_USER], 401);
        }
        if(!$wallet->business) {
            return response()->json(['message' => WalletController::BUSINESS_ONLY_FEATURE], 401);
        }

        if($wallet->hasValidOpenAccount) {
            return response()->json(['message' => 'You can\'t access this resource without an open Digital Account.'], 400);
        }

        $resourceToken = $wallet->digitalAccount->external_resource_token;
        if(!$resourceToken) {
            return response()->json(['message' => 'A Digital Account access token can\'t be found.'], 401);
        }

        $request->validate([
            'amount' => 'numeric|gte:1000',
        ], $request->all());

        $amount = $request->get('amount');


        $junoBalance = $this->getJunoBalance($resourceToken);
        if(!$junoBalance) {
            return response()->json(['message' => 'We can\'t retrieve your Digital Account balance. Try again later.'], 503);
        }

        $balance = new Balance($junoBalance);
        if($amount > $balance->transferableBalance) {
            return response()->json(['message' => "The total amount requested for transfer is greater than the total available. ($amount) > ($balance->transferableBalance)"], 400);
        }

        $transferService = new TransferService();

        $url = route('notifications.juno.transferStatusChanged', ['nickname' => $wallet->user->nickname]);

        $withdraw = $transferService->openWithdraw($wallet, $amount, $url);
        if(!$withdraw) {
            return response()->json(['message' => "We can't request your withdraw."], 400);
        }

        return response()->json(['message' => "Withdraw successfully opened.", 'withdraw' => $withdraw->transformForRequest()]);
    }

    /**
     * @param $resourceToken
     * @return mixed
     */
    private function getJunoBalance($resourceToken): mixed
    {
        $balanceService = new BalanceService([], $resourceToken);
        $junoBalance = $balanceService->retrieveBalance();
        return $junoBalance;
    }

    public function transferStatusChanged(Request $request, string $nickname)
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

            $withdraw = Withdraw::where('external_id', $transferExternalId)->first();
            if(!$withdraw) {
                $errors[] = $error = 'No Withdraw can be found with the given entityId.';
                Log::error($logIdentifier . ' - Failed: ' . $error, ['entityId' => $transferExternalId]);
                continue;
            }

            $withdraw->external_status = $changed['attributes']['status'];
            $withdraw->transfered_at = $changed['attributes']['transferDate'];
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

        return response()->json(['message' => 'DigitalAccount Withdraw status successfully updated.']);
    }
}
