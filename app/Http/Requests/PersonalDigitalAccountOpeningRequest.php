<?php

namespace App\Http\Requests;

use App\Integrations\Juno\Models\CompanyDigitalAccount;
use Illuminate\Foundation\Http\FormRequest;

class PersonalDigitalAccountOpeningRequest extends FormRequest
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

        return [
            'accountType' => 'required|in:PF',
            'type' => 'required|string|in:PAYMENT',
            'name' => 'required|string',
            'document' => 'required|string',
            'email' => 'required|email',
            'birthDate' => 'required|date|gte:' . $majority,
            'phone' => 'required|string|max:16',
            'businessArea' => 'required|string',
            'lineOfBusiness' => 'required|string',
            'companyType' => 'required|string|in:' . $companies,

            //Address
            'address.street'       => 'required|string',
            'address.number'       => 'required|string',
            'address.neighborhood' => 'required|string',
            'address.city'         => 'required|string',
            'address.state'        => 'required|string',
            'address.post_code'    => 'required|string',
            'address.complement'   => 'sometimes|string',

            //Bank Account
            'bankAccount.bankNumber' => 'required|string|max:3',
            'bankAccount.agencyNumber' => 'required|string',
            'bankAccount.accountNumber' => 'required|string',
            'bankAccount.accountComplementNumber' => 'required|string|max:3|in:' . $complementNumbers,
            'bankAccount.accountType' => 'required|string|in:CHECKINGS,SAVINGS',
            'bankAccount.accountHolder.name' => 'required|string',
            'bankAccount.accountHolder.document' => 'required|string',

            'pep' => 'required',

        ];
    }
}
