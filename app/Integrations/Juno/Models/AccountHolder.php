<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 07/07/2021
 * Time: 16:43
 */

namespace App\Integrations\Juno\Models;


class AccountHolder extends Model
{
    public $name;
    public $document;

    /**
     * AccountHolder constructor.
     * @param $name
     * @param $document
     */
    public function __construct(string $name, string $document)
    {
        $this->name = $name;
        $this->document = $document;
    }
}