<?php


namespace App\Integrations\Juno\Http;


class ResourceRequester extends \TamoJuno\ResourceRequester
{
    public function __construct($args = [], $resourceToken = null)
    {
        //parent::__construct();
        $this->client = new Client($args, $resourceToken);
    }
}
