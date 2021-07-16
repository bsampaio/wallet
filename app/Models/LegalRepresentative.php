<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LegalRepresentative
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $document
 * @property Carbon $birth_date
 * @property string $mother_name
 * @property string $type
 * @property int $digital_account_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class LegalRepresentative extends Model
{
    use HasFactory;

    protected $table = 'digital_accounts_legal_representatives';

    public function digitalAccount()
    {
        return $this->belongsTo(DigitalAccount::class);
    }
}
