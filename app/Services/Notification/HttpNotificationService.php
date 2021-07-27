<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 27/07/2021
 * Time: 14:54
 */

namespace App\Services\Notification;


use GuzzleHttp\Client;

abstract class HttpNotificationService
{
    protected $client;

    public function __construct($base_uri, $headers = [])
    {
        $this->client = new Client([
            'verify'          => false,
            'base_uri'        => $base_uri,
            'allow_redirects' => false,
            'json'            => true,
            'headers'         => array_merge([
                'User-Agent'    => 'LIFEPET-WALLET',
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json'
            ], $headers)
        ]);
    }

    public function get($uri, array $options = [])
    {
        $uri = "/api" . $uri;
        $response = $this->client->get($uri, $options);

        if($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }

    public function post($uri, $options = [])
    {
        $uri = "/api" . $uri;
        $response = $this->client->post($uri, $options);
        if($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }
}