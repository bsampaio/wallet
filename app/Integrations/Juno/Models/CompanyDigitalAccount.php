<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 07/07/2021
 * Time: 16:34
 */

namespace App\Integrations\Juno\Models;


use Carbon\Carbon;

class CompanyDigitalAccount extends DigitalAccount
{
    protected $companyType;
    protected $legalRepresentative;
    protected $cnae;
    protected $establishmentDate;
    protected $companyMembers;

    /**
     * CompanyDigitalAccount constructor.
     * @param string $type
     * @param string $name
     * @param string $document
     * @param string $email
     * @param string $phone
     * @param string $businessArea
     * @param string $linesOfBusiness
     * @param Address $address
     * @param BankAccount $bankAccount
     * @param float $monthlyIncomeOrRevenue
     * @param string $companyType
     * @param LegalRepresentative $legalRepresentative
     * @param string $cnae
     * @param Carbon $establishmentDate
     * @param array $companyMembers
     * @param bool $pep
     * @param bool $emailOptOut
     * @param bool $autoTransfer
     */
    public function __construct(string $type, string $name, string $document, string $email, string $phone, string $businessArea, string $linesOfBusiness, Address $address, BankAccount $bankAccount, float $monthlyIncomeOrRevenue, string $companyType, LegalRepresentative $legalRepresentative, string $cnae, Carbon $establishmentDate, array $companyMembers = [], $pep = false, bool $emailOptOut = true, bool $autoTransfer = false)
    {
        parent::__construct($type, $name, $document, $email, $phone, $businessArea, $linesOfBusiness, $address, $bankAccount,  $monthlyIncomeOrRevenue, $pep, $emailOptOut, $autoTransfer);
        $this->companyType = $companyType;
        $this->legalRepresentative = $legalRepresentative;
        $this->cnae = $cnae;
        $this->establishmentDate = $establishmentDate;
        $this->companyMembers = $companyMembers;
    }

    /**
     * @return array
     */
    public function getLegalRepresentative(): array
    {
        return $this->legalRepresentative->toArray();
    }

    public function getEstablishmentDate()
    {
        return $this->establishmentDate->format('Y-m-d');
    }

    public function getCompanyMembers()
    {
        $companyMembers = [];

        foreach($this->companyMembers as $companyMember) {
            $companyMembers[] = $companyMember->toArray();
        }

        return $companyMembers;
    }
}