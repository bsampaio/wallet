<?php


namespace App\Integrations\Juno\Http;


class ResourceRequester extends \TamoJuno\ResourceRequester
{
    public function __construct()
    {
        //parent::__construct();
        $this->client = new Client();
    }
}
