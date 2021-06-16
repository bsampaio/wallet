<?php


namespace App\Integrations\Juno\Http;

use GuzzleHttp\Exception\GuzzleException;
use TamoJuno\Config;

class Client extends \TamoJuno\Http\Client
{
    public function __construct(array $config = [])
    {
        try {
            $config = array_merge([
                'base_uri' => getenv('JUNO__RESOURCE_URL'),
                'headers' => [
                    'Content-Type' => 'application/json;charset=utf-8',
                    'X-Api-Version' => '2',
                    'X-Resource-Token' => getenv('JUNO__PRIVATE_TOKEN'),
                    'Authorization' => 'Bearer ' . $this->generateAuthenticationCurl(),
                ]
            ], $config);
        } catch (GuzzleException $e) {
            print_r($e->getResponse()->getBody()->getContents());
        }
        parent::__construct($config);
    }

    private function generateAuthenticationCurl():?string
    {
        $curl = curl_init();

        $credentials = base64_encode(getenv('JUNO__CLIENT_ID') . ":" . 'JUNO__CLIENT_SECRET');

        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv('JUNO__AUTH_URL') . '/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "grant_type=client_credentials",
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic '. $credentials,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return $response['access_token'];
    }
}
