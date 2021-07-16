<?php


namespace App\Integrations\Juno\Http;


use TamoJuno\Config;

abstract class Resource extends \TamoJuno\Resource
{
    public function __construct($args = [], $resourceToken = null)
    {
        $this->resource_requester = new ResourceRequester($args, $resourceToken);
    }
}
