<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DigitalAccount
 * @package App\Models
 * @property int $id
 * @property int $status
 * @property string $type
 * @property string $name
 * @property string $document
 * @property string $email
 * @property Carbon $birth_date
 * @property string $phone
 * @property string $business_area
 * @property string $lines_of_business
 * @property string $street
 * @property string $number
 * @property string $complement
 * @property string $neighborhood
 * @property string $city
 * @property string $state
 * @property string $post_code
 * @property string $ibge
 * @property string $bank_number
 * @property string $agency_number
 * @property string $account_number
 * @property string $account_complement_number
 * @property string $account_type
 * @property string $account_holder_name
 * @property string $account_holder_document
 * @property float $monthly_income_or_revenue
 * @property string $cnae
 * @property string $company_type
 * @property Carbon $establishment_date
 * @property string $external_id
 * @property string $external_type
 * @property string $external_status
 * @property string $external_document
 * @property string $external_resource_token
 * @property string $external_account_number
 * @property string $external_created_at
 * @property string $manager
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property LegalRepresentative $legalRepresentative
 */
class DigitalAccount extends Model
{
    use HasFactory;

    const STATUS__BLOCKED = -1;
    const STATUS__OPENING = 1;
    const STATUS_INACTIVE = 0;

    const EXTERNAL_STATUS__VERIFIED = 'VERIFIED';
    const EXTERNAL_STATUS__ACTIVE = 'ACTIVE';
    const EXTERNAL_STATUS__BLOCKED = 'BLOCKED';
    const EXTERNAL_STATUS__VERIFYING = 'VERIFYING';
    const EXTERNAL_STATUS__AWAITING_DOCUMENTS = 'AWAITING_DOCUMENTS';

    public function legalRepresentative()
    {
        return $this->hasOne(LegalRepresentative::class);
    }

    public function disabled(): bool
    {
        return $this->external_status !== "VERIFIED" || $this->status === self::STATUS_INACTIVE;
    }

    public static function presenter(DigitalAccount $d): array
    {
        return [
            'account_number' => $d->external_account_number,
            'company_type'   => $d->company_type,
            'status'         => $d->external_status,
            'bank_account'   => [
                'bank_number' => $d->bank_number,
                'agency_number' => $d->agency_number,
                'account_number' => $d->account_number,
                'account_type' => $d->account_type
            ]
        ];
    }
}
