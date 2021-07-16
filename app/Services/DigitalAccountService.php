<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 13/07/2021
 * Time: 11:04
 */

namespace App\Services;


use App\Http\Requests\CompanyDigitalAccountOpeningRequest;
use App\Models\DigitalAccount;
use App\Models\LegalRepresentative;
use App\Models\Wallet;
use Carbon\Carbon;

class DigitalAccountService
{
    /**
     * @param CompanyDigitalAccountOpeningRequest $request
     * @return DigitalAccount
     */
    public function createOpenAccountFromRequest(CompanyDigitalAccountOpeningRequest $request, Wallet $wallet): DigitalAccount
    {
        $digitalAccount = new DigitalAccount();
        $digitalAccount->type = 'PAYMENT';
        $digitalAccount->status = DigitalAccount::STATUS__OPENING;
        $digitalAccount->name = $request->get('name');
        $digitalAccount->document = trim($request->get('document'));
        $digitalAccount->email = trim($request->get('email'));
        if ($request->has('birthDate')) {
            $digitalAccount->birth_date = Carbon::createFromFormat('Y-m-d', $request->get('birthDate'));
        }
        $digitalAccount->phone = $request->get('phone');
        $digitalAccount->business_area = $request->get('businessArea');
        $digitalAccount->lines_of_business = $request->get('linesOfBusiness');

        $digitalAccount->street = $request->input('address.street');
        $digitalAccount->number = $request->input('address.number');
        $digitalAccount->complement = $request->input('address.complement');
        $digitalAccount->neighborhood = $request->input('address.neighborhood');
        $digitalAccount->city = $request->input('address.city');
        $digitalAccount->state = $request->input('address.state');
        $digitalAccount->post_code = $request->input('address.postCode');
        $digitalAccount->ibge = $request->input('address.ibge');

        $digitalAccount->bank_number = $request->input('bankAccount.bankNumber');
        $digitalAccount->agency_number = $request->input('bankAccount.agencyNumber');
        $digitalAccount->account_number = $request->input('bankAccount.accountNumber');
        $digitalAccount->account_complement_number = $request->input('bankAccount.accountComplementNumber');
        $digitalAccount->account_type = $request->input('bankAccount.accountType');
        $digitalAccount->account_holder_name = $request->input('bankAccount.accountHolder.name');
        $digitalAccount->account_holder_document = $request->input('bankAccount.accountHolder.document');

        $digitalAccount->monthly_income_or_revenue = $request->input('monthlyIncomeOrRevenue');

        $digitalAccount->cnae = $request->input('cnae');
        $digitalAccount->company_type = $request->input('companyType');
        $digitalAccount->establishment_date = Carbon::createFromFormat('Y-m-d', $request->input('establishmentDate'));

        $legalRepresentative = new LegalRepresentative();
        $legalRepresentative->name = $request->input('legalRepresentative.name');
        $legalRepresentative->document = $request->input('legalRepresentative.document');
        $legalRepresentative->birth_date = Carbon::createFromFormat('Y-m-d', $request->input('legalRepresentative.birthDate'));
        $legalRepresentative->mother_name = $request->input('legalRepresentative.motherName');
        $legalRepresentative->type = $request->input('legalRepresentative.type');

        $digitalAccount->wallet_id = $wallet->id;
        $digitalAccount->legalRepresentative = $legalRepresentative;

        return $digitalAccount;
    }

    public function appendJunoAdditionalData(DigitalAccount $digitalAccount, $junoResponse) {
        $digitalAccount->external_created_at = Carbon::parse($junoResponse->createdOn);
        $digitalAccount->external_status = $junoResponse->status;
        $digitalAccount->external_type = $junoResponse->type;
        $digitalAccount->external_document = $junoResponse->document;
        $digitalAccount->external_resource_token = $junoResponse->resourceToken;
        $digitalAccount->external_id = $junoResponse->id;
        $digitalAccount->external_account_number = $junoResponse->id;

        return $digitalAccount;
    }
}