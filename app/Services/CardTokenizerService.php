<?php


namespace App\Services;


use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use LVR\CreditCard\CardCvc;
use LVR\CreditCard\CardExpirationMonth;
use LVR\CreditCard\CardExpirationYear;
use LVR\CreditCard\CardNumber;

class CardTokenizerService
{

    const BASE_URI = "http://card-tokenizer.lifepet.com.br";
    //const BASE_URI = "http://localhost:3133";

    /**
     * @param string $cardNumber
     * @param string $holderName
     * @param string $securityCode
     * @param string $expirationMonth
     * @param string $expirationYear
     * @param false $validate
     * @return null
     * @throws ValidationException
     * @throws Exception
     */
    public function tokenize(string $cardNumber, string $holderName, string $securityCode, string $expirationMonth, string $expirationYear, bool $validate = false): ?string
    {
        $params = [
            'card_number' => $cardNumber,
            'expiration_year' => $expirationYear,
            'expiration_month' => $expirationMonth,
            'security_code' => $securityCode,
            'holder_name' => $holderName,
        ];

        if($validate) {
            $validator = Validator::make($params, [
                'card_number' => ['required', new CardNumber()],
                'holder_name' => ['required', 'string'],
                'security_code' => ['required', new CardCvc($cardNumber)],
                'expiration_month' => ['required', new CardExpirationMonth($expirationYear)],
                'expiration_year' => ['required', new CardExpirationYear($expirationMonth)]
            ]);
            $validator->validate();
        }

        $encodedParams = [];
        foreach($params as $key => $p) {
            $encodedParams[$key] = urlencode($p);
        }

        return $this->callService($encodedParams);
    }

    /**
     * @param array $encodedParams
     * @return string|null
     * @throws Exception
     */
    private function callService(array $encodedParams): ?string
    {
        $ch = curl_init();
        $uri = self::BASE_URI;
        if(env('APP_ENV') !== 'production') {
            $service = 'card-tokenizer-staging';
            $uri = str_replace('card-tokenizer', $service, $uri);
        }

        $url = $uri . '/tokenize?' . http_build_query($encodedParams);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            throw new Exception($error);
        }
        curl_close($ch);

        $result = json_decode($result);
        if ($result->success) {
            return $result->hash;
        }

        return null;
    }
}
