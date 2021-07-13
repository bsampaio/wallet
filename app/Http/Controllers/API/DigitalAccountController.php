<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyDigitalAccountOpeningRequest;
use App\Integrations\Juno\Services\DataService;
use App\Models\DigitalAccount;
use App\Services\DigitalAccountService;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Psy\Util\Json;

class DigitalAccountController extends Controller
{
    private $digitalAccountService = null;
    private $walletService = null;


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

        dd($junoResponse);

        $digitalAccount->save();
    }

    public function inspect()
    {

    }

    public function requestTransfer()
    {

    }

    public function enable()
    {

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
}
