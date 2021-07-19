<?php


namespace App\Integrations\Juno\Services;


use App\Integrations\Juno\Http\Resource;
use App\Integrations\Juno\Models\AccountHolder;
use App\Integrations\Juno\Models\Address;
use App\Integrations\Juno\Models\BankAccount;
use App\Integrations\Juno\Models\CompanyDigitalAccount;
use App\Integrations\Juno\Models\LegalRepresentative;
use App\Models\DigitalAccount;

class DigitalAccountService extends Resource
{
    public function endpoint(): string
    {
        return 'digital-accounts';
    }

    public function createDigitalAccount(DigitalAccount $digitalAccount, array $companyMembers = [])
    {
        $form_params = [];
        $address = new Address(
            $digitalAccount->street, $digitalAccount->number, $digitalAccount->neighborhood, $digitalAccount->city,
            $digitalAccount->state, $digitalAccount->post_code, $digitalAccount->complement
        );
        $bankAccount = new BankAccount(
            $digitalAccount->bank_number, $digitalAccount->agency_number, $digitalAccount->account_number, $digitalAccount->account_type,
            (new AccountHolder($digitalAccount->account_holder_name, $digitalAccount->account_holder_document)),
            $digitalAccount->account_complement_number
        );
        $legalRepresentative = new LegalRepresentative(
            $digitalAccount->legalRepresentative->name, $digitalAccount->legalRepresentative->document,
            $digitalAccount->legalRepresentative->birth_date, $digitalAccount->legalRepresentative->mother_name,
            $digitalAccount->legalRepresentative->type
        );
        $junoDigitalAccountModel = new CompanyDigitalAccount(
            $digitalAccount->type, $digitalAccount->name, $digitalAccount->document,
            $digitalAccount->email, $digitalAccount->phone, $digitalAccount->business_area, $digitalAccount->lines_of_business,
            $address, $bankAccount, $digitalAccount->monthly_income_or_revenue, $digitalAccount->company_type,
            $legalRepresentative, $digitalAccount->cnae, $digitalAccount->establishment_date, $companyMembers
        );

        //dd($junoDigitalAccountModel->toArray());

        return $this->create($junoDigitalAccountModel->toArray());
    }

    public function retrieveDigitalAccount()
    {
        return $this->retrieveAll();
    }

    public function updateDigitalAccount(array $form_params = [])
    {
        return $this->updateSome($form_params);
    }
}
