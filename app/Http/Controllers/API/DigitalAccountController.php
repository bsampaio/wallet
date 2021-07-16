<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyDigitalAccountOpeningRequest;
use App\Integrations\Juno\Services\DataService;
use App\Integrations\Juno\Services\DocumentService;
use App\Integrations\Juno\Services\NewOnboardingService;
use App\Integrations\Juno\Services\WebhookService;
use App\Models\DigitalAccount;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Webhook;
use App\Services\DigitalAccountService;
use App\Services\WalletService;
use Carbon\Carbon;
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

        //Convert to Juno Object Request
        $junoDigitalAccountService = new \App\Integrations\Juno\Services\DigitalAccountService();
        $junoResponse = $junoDigitalAccountService->createDigitalAccount($digitalAccount);
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
        $webhookResponse = $webhooksService->create([
            'url' => route('notifications.juno.digital-accounts.changed', ['nickname' => $wallet->user->nickname]),
            'eventTypes' => [
                'DIGITAL_ACCOUNT_STATUS_CHANGED',
            ]
        ]);


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
        }

        Log::info('digitalAccountStatusChanged() - Success:', ['request' => $request, 'payload' => $payload]);
    }
}
