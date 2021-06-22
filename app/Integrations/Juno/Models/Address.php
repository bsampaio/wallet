<?php


namespace App\Integrations\Juno\Models;


use App\Integrations\Juno\Contracts\Arrayable;

class Address extends Model
{
    protected $street;
    protected $number;
    protected $complement;
    protected $neighbourhood;
    protected $city;
    protected $state;
    protected $postCode;

    /**
     * Address constructor.
     * @param $street
     * @param $number
     * @param $complement
     * @param $neighbourhood
     * @param $city
     * @param $state
     * @param $postCode
     */
    public function __construct($street, $number, $neighbourhood, $city, $state, $postCode, $complement = null)
    {
        $this->street = $street;
        $this->number = $number;
        $this->neighbourhood = $neighbourhood;
        $this->city = $city;
        $this->state = $state;
        $this->postCode = $postCode;
        $this->complement = $complement;
    }


}
