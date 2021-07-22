# Endpoints


## API Version

<small class="badge badge-darkred">requires authentication</small>

Gets the API basic info.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


> Example response (200):

```json
{
    "environment": "local",
    "name": "Lifepet Wallet",
    "framework": "Laravel",
    "version": "8.49.0"
}
```
<div id="execution-results-GETapi" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi"></code></pre>
</div>
<div id="execution-error-GETapi" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi"></code></pre>
</div>
<form id="form-GETapi" data-method="GET" data-path="api" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api</code></b>
</p>
<p>
<label id="auth-GETapi" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi" data-component="header"></label>
</p>
</form>


## api/wallet

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/wallet',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-wallet" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet"></code></pre>
</div>
<form id="form-POSTapi-wallet" data-method="POST" data-path="api/wallet" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet" data-component="header"></label>
</p>
</form>


## api/charge

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/charge',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/charge"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


> Example response (422):

```json
{
    "errors": [
        "The reference field is required."
    ]
}
```
<div id="execution-results-GETapi-charge" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-charge"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-charge"></code></pre>
</div>
<div id="execution-error-GETapi-charge" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-charge"></code></pre>
</div>
<form id="form-GETapi-charge" data-method="GET" data-path="api/charge" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-charge', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/charge</code></b>
</p>
<p>
<label id="auth-GETapi-charge" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-charge" data-component="header"></label>
</p>
</form>


## api/utility/qrcode

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/utility/qrcode',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/utility/qrcode"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-utility-qrcode" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-utility-qrcode"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-utility-qrcode"></code></pre>
</div>
<div id="execution-error-POSTapi-utility-qrcode" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-utility-qrcode"></code></pre>
</div>
<form id="form-POSTapi-utility-qrcode" data-method="POST" data-path="api/utility/qrcode" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-utility-qrcode', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/utility/qrcode</code></b>
</p>
<p>
<label id="auth-POSTapi-utility-qrcode" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-utility-qrcode" data-component="header"></label>
</p>
</form>


## api/cards/tokenize

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/cards/tokenize',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/cards/tokenize"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-cards-tokenize" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-cards-tokenize"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-cards-tokenize"></code></pre>
</div>
<div id="execution-error-POSTapi-cards-tokenize" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-cards-tokenize"></code></pre>
</div>
<form id="form-POSTapi-cards-tokenize" data-method="POST" data-path="api/cards/tokenize" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-cards-tokenize', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/cards/tokenize</code></b>
</p>
<p>
<label id="auth-POSTapi-cards-tokenize" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-cards-tokenize" data-component="header"></label>
</p>
</form>


## Proceeds with digital account opening.

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/digital-accounts',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
        'json' => [
            'accountType' => 'PF',
            'type' => 'PAYMENT',
            'name' => 'ut',
            'document' => 'in',
            'email' => 'rsauer@example.net',
            'phone' => 'saepe',
            'businessArea' => 'et',
            'linesOfBusiness' => 'ut',
            'address' => [
                'street' => 'repellendus',
                'number' => 'perferendis',
                'neighborhood' => 'aut',
                'city' => 'nemo',
                'state' => 'maiores',
                'postCode' => 'quia',
                'complement' => 'voluptates',
            ],
            'bankAccount' => [
                'bankNumber' => 'sunt',
                'agencyNumber' => 'hic',
                'accountNumber' => 'quas',
                'accountComplementNumber' => '006',
                'accountType' => 'CHECKING',
                'accountHolder' => [
                    'name' => 'voluptas',
                    'document' => 'tempora',
                ],
            ],
            'monthlyIncomeOrRevenue' => 32438.112671,
            'pep' => 'assumenda',
            'companyType' => 'INSTITUITION_NGO_ASSOCIATION',
            'cnae' => 'sit',
            'establishmentDate' => '2021-07-20T19:25:27-0300',
            'legalRepresentative' => [
                'name' => 'distinctio',
                'document' => 'debitis',
                'birthDate' => '2021-07-20T19:25:27-0300',
                'motherName' => 'aut',
                'type' => 'MEMBER',
            ],
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/digital-accounts"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "accountType": "PF",
    "type": "PAYMENT",
    "name": "ut",
    "document": "in",
    "email": "rsauer@example.net",
    "phone": "saepe",
    "businessArea": "et",
    "linesOfBusiness": "ut",
    "address": {
        "street": "repellendus",
        "number": "perferendis",
        "neighborhood": "aut",
        "city": "nemo",
        "state": "maiores",
        "postCode": "quia",
        "complement": "voluptates"
    },
    "bankAccount": {
        "bankNumber": "sunt",
        "agencyNumber": "hic",
        "accountNumber": "quas",
        "accountComplementNumber": "006",
        "accountType": "CHECKING",
        "accountHolder": {
            "name": "voluptas",
            "document": "tempora"
        }
    },
    "monthlyIncomeOrRevenue": 32438.112671,
    "pep": "assumenda",
    "companyType": "INSTITUITION_NGO_ASSOCIATION",
    "cnae": "sit",
    "establishmentDate": "2021-07-20T19:25:27-0300",
    "legalRepresentative": {
        "name": "distinctio",
        "document": "debitis",
        "birthDate": "2021-07-20T19:25:27-0300",
        "motherName": "aut",
        "type": "MEMBER"
    }
}

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response => response.json());
```


<div id="execution-results-POSTapi-digital-accounts" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-digital-accounts"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-digital-accounts"></code></pre>
</div>
<div id="execution-error-POSTapi-digital-accounts" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-digital-accounts"></code></pre>
</div>
<form id="form-POSTapi-digital-accounts" data-method="POST" data-path="api/digital-accounts" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-digital-accounts', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/digital-accounts</code></b>
</p>
<p>
<label id="auth-POSTapi-digital-accounts" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-digital-accounts" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
<p>
<b><code>accountType</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="accountType" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>
The value must be one of <code>PF</code> or <code>PJ</code>.
</p>
<p>
<b><code>type</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="type" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>
The value must be one of <code>PAYMENT</code>.
</p>
<p>
<b><code>name</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="name" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>document</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="document" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>email</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="email" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>
The value must be a valid email address.
</p>
<p>
<b><code>phone</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="phone" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>businessArea</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="businessArea" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>linesOfBusiness</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="linesOfBusiness" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<details>
<summary>
<b><code>address</code></b>&nbsp;&nbsp;<small>object</small>     <i>optional</i> &nbsp;
<br>

</summary>
<br>
<p>
<b><code>address.street</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="address.street" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>address.number</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="address.number" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>address.neighborhood</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="address.neighborhood" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>address.city</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="address.city" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>address.state</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="address.state" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>address.postCode</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="address.postCode" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>address.complement</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
<input type="text" name="address.complement" data-endpoint="POSTapi-digital-accounts" data-component="body"  hidden>
<br>

</p>
</details>
</p>
<p>
<details>
<summary>
<b><code>bankAccount</code></b>&nbsp;&nbsp;<small>object</small>     <i>optional</i> &nbsp;
<br>

</summary>
<br>
<p>
<b><code>bankAccount.bankNumber</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="bankAccount.bankNumber" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>bankAccount.agencyNumber</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="bankAccount.agencyNumber" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>bankAccount.accountNumber</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="bankAccount.accountNumber" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>bankAccount.accountComplementNumber</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
<input type="text" name="bankAccount.accountComplementNumber" data-endpoint="POSTapi-digital-accounts" data-component="body"  hidden>
<br>
The value must be one of <code>001</code>, <code>002</code>, <code>003</code>, <code>006</code>, <code>007</code>, <code>013</code>, <code>022</code>, <code>023</code>, <code>028</code>, <code>043</code>, or <code>031</code>.
</p>
<p>
<b><code>bankAccount.accountType</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="bankAccount.accountType" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>
The value must be one of <code>CHECKING</code> or <code>SAVINGS</code>.
</p>
<p>
<details>
<summary>
<b><code>bankAccount.accountHolder</code></b>&nbsp;&nbsp;<small>object</small>     <i>optional</i> &nbsp;
<br>

</summary>
<br>
<p>
<b><code>bankAccount.accountHolder.name</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="bankAccount.accountHolder.name" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>bankAccount.accountHolder.document</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="bankAccount.accountHolder.document" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
</details>
</p>

</details>
</p>
<p>
<b><code>monthlyIncomeOrRevenue</code></b>&nbsp;&nbsp;<small>number</small>  &nbsp;
<input type="number" name="monthlyIncomeOrRevenue" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>pep</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="pep" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>companyType</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="companyType" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>
The value must be one of <code>MEI</code>, <code>EI</code>, <code>EIRELI</code>, <code>SA</code>, <code>LTDA</code>, or <code>INSTITUITION_NGO_ASSOCIATION</code>.
</p>
<p>
<b><code>cnae</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="cnae" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>establishmentDate</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="establishmentDate" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>
The value must be a valid date.
</p>
<p>
<details>
<summary>
<b><code>legalRepresentative</code></b>&nbsp;&nbsp;<small>object</small>     <i>optional</i> &nbsp;
<br>

</summary>
<br>
<p>
<b><code>legalRepresentative.name</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="legalRepresentative.name" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>legalRepresentative.document</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="legalRepresentative.document" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>legalRepresentative.birthDate</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="legalRepresentative.birthDate" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>
The value must be a valid date.
</p>
<p>
<b><code>legalRepresentative.motherName</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="legalRepresentative.motherName" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>

</p>
<p>
<b><code>legalRepresentative.type</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="legalRepresentative.type" data-endpoint="POSTapi-digital-accounts" data-component="body" required  hidden>
<br>
The value must be one of <code>INDIVIDUAL</code>, <code>ATTORNEY</code>, <code>DESIGNEE</code>, <code>MEMBER</code>, <code>DIRECTOR</code>, or <code>PRESIDENT</code>.
</p>
</details>
</p>

</form>


## api/digital-accounts/business-areas

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/digital-accounts/business-areas',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/digital-accounts/business-areas"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


> Example response (200):

```json
[
    {
        "code": 1000,
        "activity": "Produtos",
        "category": "Acessorios automotivos"
    },
    {
        "code": 1001,
        "activity": "Produtos",
        "category": "Agronomia e agricultura"
    },
    {
        "code": 1002,
        "activity": "Produtos",
        "category": "Agropecuária e venda de animais"
    },
    {
        "code": 1003,
        "activity": "Produtos",
        "category": "Alimentos e Bebidas"
    },
    {
        "code": 1004,
        "activity": "Produtos",
        "category": "Antiguidades e colecionaveis"
    },
    {
        "code": 1005,
        "activity": "Produtos",
        "category": "Arte e artesanato"
    },
    {
        "code": 1006,
        "activity": "Produtos",
        "category": "Artigos religiosos"
    },
    {
        "code": 1007,
        "activity": "Produtos",
        "category": "Beleza e cuidados pessoais"
    },
    {
        "code": 1008,
        "activity": "Produtos",
        "category": "Brinquedos e Jogos"
    },
    {
        "code": 1009,
        "activity": "Produtos",
        "category": "Construção civil e alvenaria"
    },
    {
        "code": 1010,
        "activity": "Produtos",
        "category": "Eletrodomesticos"
    },
    {
        "code": 1011,
        "activity": "Produtos",
        "category": "Esporte e lazer"
    },
    {
        "code": 1012,
        "activity": "Produtos",
        "category": "Gestante e bebê"
    },
    {
        "code": 1013,
        "activity": "Produtos",
        "category": "Joias e relojoaria"
    },
    {
        "code": 1014,
        "activity": "Produtos",
        "category": "Livros, apostilas, Cds e DVDs"
    },
    {
        "code": 1015,
        "activity": "Produtos",
        "category": "Maquinário industrial"
    },
    {
        "code": 1016,
        "activity": "Produtos",
        "category": "Material de limpeza"
    },
    {
        "code": 1017,
        "activity": "Produtos",
        "category": "Moda e vestuário"
    },
    {
        "code": 1018,
        "activity": "Produtos",
        "category": "Móveis e decoração"
    },
    {
        "code": 1019,
        "activity": "Produtos",
        "category": "Instrumentos musicais e equipamentos sonoros"
    },
    {
        "code": 1020,
        "activity": "Produtos",
        "category": "Papelaria e escritório"
    },
    {
        "code": 1021,
        "activity": "Produtos",
        "category": "Pet Shop"
    },
    {
        "code": 1022,
        "activity": "Produtos",
        "category": "Saúde e suplementos"
    },
    {
        "code": 1023,
        "activity": "Produtos",
        "category": "Sex Shop e artigos adultos"
    },
    {
        "code": 1024,
        "activity": "Produtos",
        "category": "Tecnologia, eletronicos e informatica"
    },
    {
        "code": 1025,
        "activity": "Produtos",
        "category": "Utensílios e objetos em geral"
    },
    {
        "code": 1026,
        "activity": "Produtos",
        "category": "Outros"
    },
    {
        "code": 1027,
        "activity": "Produtos",
        "category": "Drop Shipping"
    },
    {
        "code": 2000,
        "activity": "Serviços",
        "category": "Academia e esportes"
    },
    {
        "code": 2001,
        "activity": "Serviços",
        "category": "Advocacia"
    },
    {
        "code": 2002,
        "activity": "Serviços",
        "category": "Agronomia e agricultura"
    },
    {
        "code": 2003,
        "activity": "Serviços",
        "category": "Aluguel e condominio"
    },
    {
        "code": 2004,
        "activity": "Serviços",
        "category": "Clube de descontos e benefícios"
    },
    {
        "code": 2005,
        "activity": "Serviços",
        "category": "Veterinária e pet shop"
    },
    {
        "code": 2006,
        "activity": "Serviços",
        "category": "Arquitetura e decoração"
    },
    {
        "code": 2007,
        "activity": "Serviços",
        "category": "Assistencia tecnica"
    },
    {
        "code": 2008,
        "activity": "Serviços",
        "category": "Aulas Particulares"
    },
    {
        "code": 2009,
        "activity": "Serviços",
        "category": "Jardim e Botânica"
    },
    {
        "code": 2010,
        "activity": "Serviços",
        "category": "Cobranças e Dividas"
    },
    {
        "code": 2011,
        "activity": "Serviços",
        "category": "Construção civil e alvenaria"
    },
    {
        "code": 2012,
        "activity": "Serviços",
        "category": "Consultoria"
    },
    {
        "code": 2013,
        "activity": "Serviços",
        "category": "Contabilidade"
    },
    {
        "code": 2014,
        "activity": "Serviços",
        "category": "Cursos e treinamentos"
    },
    {
        "code": 2015,
        "activity": "Serviços",
        "category": "Desenvolvimento de sites aplicativos e afins"
    },
    {
        "code": 2016,
        "activity": "Serviços",
        "category": "Eventos, festas e entretenimento"
    },
    {
        "code": 2017,
        "activity": "Serviços",
        "category": "Hidráulica e elétrica"
    },
    {
        "code": 2018,
        "activity": "Serviços",
        "category": "Instituições de ensino"
    },
    {
        "code": 2019,
        "activity": "Serviços",
        "category": "Internet, hospedagem e dominio"
    },
    {
        "code": 2020,
        "activity": "Serviços",
        "category": "Limpeza e manutenção"
    },
    {
        "code": 2021,
        "activity": "Serviços",
        "category": "Manutençao de veículos"
    },
    {
        "code": 2022,
        "activity": "Serviços",
        "category": "Design, Marketing, fotografia e audiovisual"
    },
    {
        "code": 2023,
        "activity": "Serviços",
        "category": "Profissionais da saúde"
    },
    {
        "code": 2024,
        "activity": "Serviços",
        "category": "Representação comercial"
    },
    {
        "code": 2025,
        "activity": "Serviços",
        "category": "Registro de marcas e patentes"
    },
    {
        "code": 2026,
        "activity": "Serviços",
        "category": "Saúde e Beleza"
    },
    {
        "code": 2027,
        "activity": "Serviços",
        "category": "Segurança e sistema de controle"
    },
    {
        "code": 2028,
        "activity": "Serviços",
        "category": "Seguros, Planos e corretagem"
    },
    {
        "code": 2029,
        "activity": "Serviços",
        "category": "Serralheria e vidraçaria"
    },
    {
        "code": 2030,
        "activity": "Serviços",
        "category": "Transporte e logística"
    },
    {
        "code": 2031,
        "activity": "Serviços",
        "category": "Turismo e viagens"
    },
    {
        "code": 2032,
        "activity": "Serviços",
        "category": "TVs e sinais de antena"
    },
    {
        "code": 2033,
        "activity": "Serviços",
        "category": "Outros"
    },
    {
        "code": 3000,
        "activity": "ONGs, Associações e Afins",
        "category": "ONGs, Associações e Afins"
    }
]
```
<div id="execution-results-GETapi-digital-accounts-business-areas" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-digital-accounts-business-areas"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-digital-accounts-business-areas"></code></pre>
</div>
<div id="execution-error-GETapi-digital-accounts-business-areas" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-digital-accounts-business-areas"></code></pre>
</div>
<form id="form-GETapi-digital-accounts-business-areas" data-method="GET" data-path="api/digital-accounts/business-areas" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-digital-accounts-business-areas', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/digital-accounts/business-areas</code></b>
</p>
<p>
<label id="auth-GETapi-digital-accounts-business-areas" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-digital-accounts-business-areas" data-component="header"></label>
</p>
</form>


## api/digital-accounts/banks

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/digital-accounts/banks',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/digital-accounts/banks"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


> Example response (200):

```json
[
    {
        "number": "001",
        "name": "BCO DO BRASIL S.A."
    },
    {
        "number": "003",
        "name": "BCO DA AMAZONIA S.A."
    },
    {
        "number": "004",
        "name": "BCO DO NORDESTE DO BRASIL S.A."
    },
    {
        "number": "021",
        "name": "BCO BANESTES S.A."
    },
    {
        "number": "025",
        "name": "BCO ALFA S.A."
    },
    {
        "number": "033",
        "name": "BCO SANTANDER (BRASIL) S.A."
    },
    {
        "number": "036",
        "name": "BCO BBI S.A."
    },
    {
        "number": "037",
        "name": "BCO DO EST. DO PA S.A."
    },
    {
        "number": "041",
        "name": "BCO DO ESTADO DO RS S.A."
    },
    {
        "number": "047",
        "name": "BCO DO EST. DE SE S.A."
    },
    {
        "number": "062",
        "name": "HIPERCARD BM S.A."
    },
    {
        "number": "066",
        "name": "BCO MORGAN STANLEY S.A."
    },
    {
        "number": "069",
        "name": "BCO CREFISA S.A."
    },
    {
        "number": "070",
        "name": "BRB - BCO DE BRASILIA S.A."
    },
    {
        "number": "074",
        "name": "BCO. J.SAFRA S.A."
    },
    {
        "number": "077",
        "name": "Banco Inter"
    },
    {
        "number": "079",
        "name": "BCO ORIGINAL DO AGRO S\/A"
    },
    {
        "number": "084",
        "name": "UNIPRIME NORTE DO PARANÁ - CC"
    },
    {
        "number": "085",
        "name": "COOP CENTRAL AILOS"
    },
    {
        "number": "089",
        "name": "CREDISAN CC"
    },
    {
        "number": "091",
        "name": "CCCM UNICRED CENTRAL RS"
    },
    {
        "number": "093",
        "name": "POLOCRED SCMEPP LTDA."
    },
    {
        "number": "097",
        "name": "CREDISIS CENTRAL DE COOPERATIVAS DE CRÉDITO LTDA."
    },
    {
        "number": "099",
        "name": "UNIPRIME CENTRAL CCC LTDA."
    },
    {
        "number": "104",
        "name": "Caixa Economica Federal"
    },
    {
        "number": "107",
        "name": "BCO BOCOM BBM S.A."
    },
    {
        "number": "114",
        "name": "CENTRAL COOPERATIVA DE CRÉDITO NO ESTADO DO ESPÍRITO SANTO"
    },
    {
        "number": "121",
        "name": "BCO AGIBANK S.A."
    },
    {
        "number": "133",
        "name": "CRESOL CONFEDERAÇÃO"
    },
    {
        "number": "136",
        "name": "CONF NAC COOP CENTRAIS UNICRED"
    },
    {
        "number": "173",
        "name": "BRL TRUST DTVM SA"
    },
    {
        "number": "197",
        "name": "Stone Pagamentos S.A."
    },
    {
        "number": "208",
        "name": "BANCO BTG PACTUAL S.A."
    },
    {
        "number": "212",
        "name": "Banco Original"
    },
    {
        "number": "218",
        "name": "BCO BS2 S.A."
    },
    {
        "number": "222",
        "name": "BCO CRÉDIT AGRICOLE BR S.A."
    },
    {
        "number": "237",
        "name": "BCO BRADESCO S.A."
    },
    {
        "number": "243",
        "name": "BCO MÁXIMA S.A."
    },
    {
        "number": "246",
        "name": "BCO ABC BRASIL S.A."
    },
    {
        "number": "254",
        "name": "PARANA BCO S.A."
    },
    {
        "number": "260",
        "name": "Nu Pagamentos S.A."
    },
    {
        "number": "265",
        "name": "BCO FATOR S.A."
    },
    {
        "number": "290",
        "name": "PAGSEGURO S.A."
    },
    {
        "number": "301",
        "name": "BPP IP S.A."
    },
    {
        "number": "318",
        "name": "BCO BMG S.A."
    },
    {
        "number": "320",
        "name": "BCO CCB BRASIL S.A."
    },
    {
        "number": "323",
        "name": "Mercado Pago"
    },
    {
        "number": "336",
        "name": "BCO C6 S.A."
    },
    {
        "number": "341",
        "name": "ITAÚ UNIBANCO S.A."
    },
    {
        "number": "364",
        "name": "GERENCIANET"
    },
    {
        "number": "366",
        "name": "BCO SOCIETE GENERALE BRASIL"
    },
    {
        "number": "370",
        "name": "BCO MIZUHO S.A."
    },
    {
        "number": "376",
        "name": "BCO J.P. MORGAN S.A."
    },
    {
        "number": "389",
        "name": "BCO MERCANTIL DO BRASIL S.A."
    },
    {
        "number": "394",
        "name": "BCO BRADESCO FINANC. S.A."
    },
    {
        "number": "399",
        "name": "KIRTON BANK"
    },
    {
        "number": "412",
        "name": "BCO CAPITAL S.A."
    },
    {
        "number": "422",
        "name": "BCO SAFRA S.A."
    },
    {
        "number": "473",
        "name": "BCO CAIXA GERAL BRASIL S.A."
    },
    {
        "number": "600",
        "name": "BCO LUSO BRASILEIRO S.A."
    },
    {
        "number": "604",
        "name": "BCO INDUSTRIAL DO BRASIL S.A."
    },
    {
        "number": "611",
        "name": "BCO PAULISTA S.A."
    },
    {
        "number": "612",
        "name": "BCO GUANABARA S.A."
    },
    {
        "number": "613",
        "name": "OMNI BANCO S.A."
    },
    {
        "number": "623",
        "name": "BANCO PAN"
    },
    {
        "number": "626",
        "name": "BCO C6 CONSIG"
    },
    {
        "number": "630",
        "name": "SMARTBANK"
    },
    {
        "number": "633",
        "name": "BCO RENDIMENTO S.A."
    },
    {
        "number": "634",
        "name": "BCO TRIANGULO S.A."
    },
    {
        "number": "637",
        "name": "BCO SOFISA S.A."
    },
    {
        "number": "654",
        "name": "BCO DIGIMAIS S.A."
    },
    {
        "number": "655",
        "name": "BCO VOTORANTIM S.A."
    },
    {
        "number": "707",
        "name": "BCO DAYCOVAL S.A"
    },
    {
        "number": "739",
        "name": "BCO CETELEM S.A."
    },
    {
        "number": "741",
        "name": "BCO RIBEIRAO PRETO S.A."
    },
    {
        "number": "743",
        "name": "Banco Semear"
    },
    {
        "number": "745",
        "name": "BCO CITIBANK S.A."
    },
    {
        "number": "746",
        "name": "BCO MODAL S.A."
    },
    {
        "number": "748",
        "name": "BCO COOPERATIVO SICREDI S.A."
    },
    {
        "number": "752",
        "name": "BCO BNP PARIBAS BRASIL S A"
    },
    {
        "number": "755",
        "name": "BOFA MERRILL LYNCH BM S.A."
    },
    {
        "number": "756",
        "name": "BANCO SICOOB S.A."
    }
]
```
<div id="execution-results-GETapi-digital-accounts-banks" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-digital-accounts-banks"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-digital-accounts-banks"></code></pre>
</div>
<div id="execution-error-GETapi-digital-accounts-banks" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-digital-accounts-banks"></code></pre>
</div>
<form id="form-GETapi-digital-accounts-banks" data-method="GET" data-path="api/digital-accounts/banks" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-digital-accounts-banks', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/digital-accounts/banks</code></b>
</p>
<p>
<label id="auth-GETapi-digital-accounts-banks" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-digital-accounts-banks" data-component="header"></label>
</p>
</form>


## api/digital-accounts/company-types

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/digital-accounts/company-types',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/digital-accounts/company-types"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


> Example response (200):

```json
[
    "MEI",
    "EI",
    "EIRELI",
    "LTDA",
    "SA",
    "INSTITUTION_NGO_ASSOCIATION"
]
```
<div id="execution-results-GETapi-digital-accounts-company-types" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-digital-accounts-company-types"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-digital-accounts-company-types"></code></pre>
</div>
<div id="execution-error-GETapi-digital-accounts-company-types" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-digital-accounts-company-types"></code></pre>
</div>
<form id="form-GETapi-digital-accounts-company-types" data-method="GET" data-path="api/digital-accounts/company-types" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-digital-accounts-company-types', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/digital-accounts/company-types</code></b>
</p>
<p>
<label id="auth-GETapi-digital-accounts-company-types" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-digital-accounts-company-types" data-component="header"></label>
</p>
</form>


## api/digital-accounts/documents-link

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/digital-accounts/documents-link',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/digital-accounts/documents-link"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


> Example response (401):

```json
{
    "message": "There is no wallet available to this user."
}
```
<div id="execution-results-GETapi-digital-accounts-documents-link" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-digital-accounts-documents-link"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-digital-accounts-documents-link"></code></pre>
</div>
<div id="execution-error-GETapi-digital-accounts-documents-link" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-digital-accounts-documents-link"></code></pre>
</div>
<form id="form-GETapi-digital-accounts-documents-link" data-method="GET" data-path="api/digital-accounts/documents-link" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-digital-accounts-documents-link', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/digital-accounts/documents-link</code></b>
</p>
<p>
<label id="auth-GETapi-digital-accounts-documents-link" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-digital-accounts-documents-link" data-component="header"></label>
</p>
</form>


## api/digital-accounts/documents

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/digital-accounts/documents',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/digital-accounts/documents"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


> Example response (401):

```json
{
    "message": "There is no wallet available to this user."
}
```
<div id="execution-results-GETapi-digital-accounts-documents" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-digital-accounts-documents"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-digital-accounts-documents"></code></pre>
</div>
<div id="execution-error-GETapi-digital-accounts-documents" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-digital-accounts-documents"></code></pre>
</div>
<form id="form-GETapi-digital-accounts-documents" data-method="GET" data-path="api/digital-accounts/documents" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-digital-accounts-documents', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/digital-accounts/documents</code></b>
</p>
<p>
<label id="auth-GETapi-digital-accounts-documents" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-digital-accounts-documents" data-component="header"></label>
</p>
</form>


## api/digital-accounts/inspect

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/digital-accounts/inspect',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/digital-accounts/inspect"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


<div id="execution-results-GETapi-digital-accounts-inspect" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-digital-accounts-inspect"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-digital-accounts-inspect"></code></pre>
</div>
<div id="execution-error-GETapi-digital-accounts-inspect" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-digital-accounts-inspect"></code></pre>
</div>
<form id="form-GETapi-digital-accounts-inspect" data-method="GET" data-path="api/digital-accounts/inspect" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-digital-accounts-inspect', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/digital-accounts/inspect</code></b>
</p>
<p>
<label id="auth-GETapi-digital-accounts-inspect" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-digital-accounts-inspect" data-component="header"></label>
</p>
</form>


## api/digital-accounts

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/digital-accounts',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/digital-accounts"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


> Example response (401):

```json
{
    "message": "There is no wallet available to this user."
}
```
<div id="execution-results-GETapi-digital-accounts" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-digital-accounts"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-digital-accounts"></code></pre>
</div>
<div id="execution-error-GETapi-digital-accounts" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-digital-accounts"></code></pre>
</div>
<form id="form-GETapi-digital-accounts" data-method="GET" data-path="api/digital-accounts" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-digital-accounts', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/digital-accounts</code></b>
</p>
<p>
<label id="auth-GETapi-digital-accounts" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-digital-accounts" data-component="header"></label>
</p>
</form>


## api/logout

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/logout',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/logout"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-logout" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-logout"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-logout"></code></pre>
</div>
<div id="execution-error-POSTapi-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-logout"></code></pre>
</div>
<form id="form-POSTapi-logout" data-method="POST" data-path="api/logout" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-logout', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/logout</code></b>
</p>
<p>
<label id="auth-POSTapi-logout" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-logout" data-component="header"></label>
</p>
</form>


## api/user

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/user',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/user"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


> Example response (401):

```json
{
    "message": "Unauthenticated."
}
```
<div id="execution-results-GETapi-user" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-user"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-user"></code></pre>
</div>
<div id="execution-error-GETapi-user" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-user"></code></pre>
</div>
<form id="form-GETapi-user" data-method="GET" data-path="api/user" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-user', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/user</code></b>
</p>
<p>
<label id="auth-GETapi-user" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-user" data-component="header"></label>
</p>
</form>


## api/notifications/juno/digital-accounts/{nickname}/changed

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/notifications/juno/digital-accounts/quidem/changed',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/notifications/juno/digital-accounts/quidem/changed"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-notifications-juno-digital-accounts--nickname--changed" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-notifications-juno-digital-accounts--nickname--changed"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-notifications-juno-digital-accounts--nickname--changed"></code></pre>
</div>
<div id="execution-error-POSTapi-notifications-juno-digital-accounts--nickname--changed" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-notifications-juno-digital-accounts--nickname--changed"></code></pre>
</div>
<form id="form-POSTapi-notifications-juno-digital-accounts--nickname--changed" data-method="POST" data-path="api/notifications/juno/digital-accounts/{nickname}/changed" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-notifications-juno-digital-accounts--nickname--changed', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/notifications/juno/digital-accounts/{nickname}/changed</code></b>
</p>
<p>
<label id="auth-POSTapi-notifications-juno-digital-accounts--nickname--changed" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-notifications-juno-digital-accounts--nickname--changed" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
<p>
<b><code>nickname</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="nickname" data-endpoint="POSTapi-notifications-juno-digital-accounts--nickname--changed" data-component="url" required  hidden>
<br>

</p>
</form>


## api/notifications/juno

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/notifications/juno',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/notifications/juno"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


<div id="execution-results-GETapi-notifications-juno" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-notifications-juno"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-notifications-juno"></code></pre>
</div>
<div id="execution-error-GETapi-notifications-juno" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-notifications-juno"></code></pre>
</div>
<form id="form-GETapi-notifications-juno" data-method="GET" data-path="api/notifications/juno" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-notifications-juno', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/notifications/juno</code></b>
</p>
<p>
<label id="auth-GETapi-notifications-juno" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-notifications-juno" data-component="header"></label>
</p>
</form>


## api/notifications/juno

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/notifications/juno',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/notifications/juno"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-notifications-juno" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-notifications-juno"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-notifications-juno"></code></pre>
</div>
<div id="execution-error-POSTapi-notifications-juno" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-notifications-juno"></code></pre>
</div>
<form id="form-POSTapi-notifications-juno" data-method="POST" data-path="api/notifications/juno" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-notifications-juno', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/notifications/juno</code></b>
</p>
<p>
<label id="auth-POSTapi-notifications-juno" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-notifications-juno" data-component="header"></label>
</p>
</form>


## Enables wallet for user

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/wallet/accusamus/enable',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet/accusamus/enable"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Wallet-Key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-wallet--nickname--enable" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet--nickname--enable"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet--nickname--enable"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet--nickname--enable" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet--nickname--enable"></code></pre>
</div>
<form id="form-POSTapi-wallet--nickname--enable" data-method="POST" data-path="api/wallet/{nickname}/enable" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet--nickname--enable', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/{nickname}/enable</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet--nickname--enable" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet--nickname--enable" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
<p>
<b><code>nickname</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="nickname" data-endpoint="POSTapi-wallet--nickname--enable" data-component="url" required  hidden>
<br>

</p>
</form>


## Allows to transfer available balance to another wallet

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/wallet/transfer',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet/transfer"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Wallet-Key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-wallet-transfer" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet-transfer"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet-transfer"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet-transfer" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet-transfer"></code></pre>
</div>
<form id="form-POSTapi-wallet-transfer" data-method="POST" data-path="api/wallet/transfer" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet-transfer', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/transfer</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet-transfer" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet-transfer" data-component="header"></label>
</p>
</form>


## Create a charge

<small class="badge badge-darkred">requires authentication</small>

This method will generate info to charge another wallet.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/wallet/charge',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet/charge"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Wallet-Key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-wallet-charge" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet-charge"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet-charge"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet-charge" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet-charge"></code></pre>
</div>
<form id="form-POSTapi-wallet-charge" data-method="POST" data-path="api/wallet/charge" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet-charge', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/charge</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet-charge" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet-charge" data-component="header"></label>
</p>
</form>


## api/wallet/payment/credit-card

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/wallet/payment/credit-card',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet/payment/credit-card"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Wallet-Key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-wallet-payment-credit-card" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet-payment-credit-card"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet-payment-credit-card"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet-payment-credit-card" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet-payment-credit-card"></code></pre>
</div>
<form id="form-POSTapi-wallet-payment-credit-card" data-method="POST" data-path="api/wallet/payment/credit-card" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet-payment-credit-card', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/payment/credit-card</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet-payment-credit-card" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet-payment-credit-card" data-component="header"></label>
</p>
</form>


## This method allows to add balance to the wallet account

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/wallet/deposit/pix',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet/deposit/pix"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Wallet-Key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-wallet-deposit-pix" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet-deposit-pix"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet-deposit-pix"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet-deposit-pix" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet-deposit-pix"></code></pre>
</div>
<form id="form-POSTapi-wallet-deposit-pix" data-method="POST" data-path="api/wallet/deposit/pix" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet-deposit-pix', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/deposit/pix</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet-deposit-pix" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet-deposit-pix" data-component="header"></label>
</p>
</form>


## api/wallet/cards

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/wallet/cards',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet/cards"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Wallet-Key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```


> Example response (200):

```json
{
    "cards": []
}
```
<div id="execution-results-GETapi-wallet-cards" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-wallet-cards"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-wallet-cards"></code></pre>
</div>
<div id="execution-error-GETapi-wallet-cards" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-wallet-cards"></code></pre>
</div>
<form id="form-GETapi-wallet-cards" data-method="GET" data-path="api/wallet/cards" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-wallet-cards', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/wallet/cards</code></b>
</p>
<p>
<label id="auth-GETapi-wallet-cards" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-wallet-cards" data-component="header"></label>
</p>
</form>


## api/wallet/cards/add

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/wallet/cards/add',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet/cards/add"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Wallet-Key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-wallet-cards-add" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet-cards-add"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet-cards-add"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet-cards-add" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet-cards-add"></code></pre>
</div>
<form id="form-POSTapi-wallet-cards-add" data-method="POST" data-path="api/wallet/cards/add" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet-cards-add', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/cards/add</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet-cards-add" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet-cards-add" data-component="header"></label>
</p>
</form>


## api/wallet/cards/delete

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/wallet/cards/delete',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet/cards/delete"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Wallet-Key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-wallet-cards-delete" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet-cards-delete"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet-cards-delete"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet-cards-delete" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet-cards-delete"></code></pre>
</div>
<form id="form-POSTapi-wallet-cards-delete" data-method="POST" data-path="api/wallet/cards/delete" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet-cards-delete', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/cards/delete</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet-cards-delete" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet-cards-delete" data-component="header"></label>
</p>
</form>


## api/wallet/cards/activate

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/wallet/cards/activate',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet/cards/activate"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Wallet-Key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-wallet-cards-activate" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet-cards-activate"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet-cards-activate"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet-cards-activate" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet-cards-activate"></code></pre>
</div>
<form id="form-POSTapi-wallet-cards-activate" data-method="POST" data-path="api/wallet/cards/activate" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet-cards-activate', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/cards/activate</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet-cards-activate" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet-cards-activate" data-component="header"></label>
</p>
</form>


## api/wallet/cards/disable

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/wallet/cards/disable',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet/cards/disable"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Wallet-Key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-wallet-cards-disable" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet-cards-disable"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet-cards-disable"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet-cards-disable" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet-cards-disable"></code></pre>
</div>
<form id="form-POSTapi-wallet-cards-disable" data-method="POST" data-path="api/wallet/cards/disable" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet-cards-disable', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/cards/disable</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet-cards-disable" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet-cards-disable" data-component="header"></label>
</p>
</form>


## api/wallet/cards/main

<small class="badge badge-darkred">requires authentication</small>



> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.lifepet.com.br/api/wallet/cards/main',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/wallet/cards/main"
);

let headers = {
    "Heimdall-Key": "{HEIMDALL_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "Wallet-Key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response => response.json());
```


<div id="execution-results-POSTapi-wallet-cards-main" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet-cards-main"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet-cards-main"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet-cards-main" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet-cards-main"></code></pre>
</div>
<form id="form-POSTapi-wallet-cards-main" data-method="POST" data-path="api/wallet/cards/main" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet-cards-main', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/cards/main</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet-cards-main" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet-cards-main" data-component="header"></label>
</p>
</form>



