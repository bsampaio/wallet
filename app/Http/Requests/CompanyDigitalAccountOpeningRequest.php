<?php

namespace App\Http\Requests;

use App\Integrations\Juno\Models\BankAccount;
use App\Integrations\Juno\Models\CompanyDigitalAccount;
use App\Integrations\Juno\Models\LegalRepresentative;
use Illuminate\Foundation\Http\FormRequest;

class CompanyDigitalAccountOpeningRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $majority = now()->subYears(18)->format('Y-m-d');
        $companies = join(',', CompanyDigitalAccount::COMPANY_TYPES);
        $representativeTypes = join(',',LegalRepresentative::TYPES);
        $complementNumbers = join(',',BankAccount::COMPLEMENT_NUMBERS);

        return [
            'accountType' => 'required|in:PF',
            'type' => 'required|string|in:PAYMENT',
            'name' => 'required|string',
            'document' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string|max:16',
            'businessArea' => 'required|string',
            'linesOfBusiness' => 'required|string',

            //Address
            'address.street'       => 'required|string',
            'address.number'       => 'required|string',
            'address.neighborhood' => 'required|string',
            'address.city'         => 'required|string',
            'address.state'        => 'required|string',
            'address.postCode'    => 'required|string',
            'address.complement'   => 'sometimes|string',

            //Bank Account
            'bankAccount.bankNumber' => 'required|string|max:3',
            'bankAccount.agencyNumber' => 'required|string',
            'bankAccount.accountNumber' => 'required|string',
            'bankAccount.accountComplementNumber' => 'sometimes|string|max:3|in:' . $complementNumbers,
            'bankAccount.accountType' => 'required|string|in:CHECKING,SAVINGS',
            'bankAccount.accountHolder.name' => 'required|string',
            'bankAccount.accountHolder.document' => 'required|string',

            'monthlyIncomeOrRevenue' => 'required|numeric',

            'pep' => 'required',

            //Business Only
            'companyType' => 'required|string|in:' . $companies,
            'cnae' => 'required|string',
            'establishmentDate' => 'required|date',

            //Legal representative
            'legalRepresentative.name' => 'required|string',
            'legalRepresentative.document' => 'required|string',
            'legalRepresentative.birthDate' => 'required|date|before:now',
            'legalRepresentative.motherName' => 'required|string',
            'legalRepresentative.type' => 'required|string|in:' . $representativeTypes,
        ];
    }
}
