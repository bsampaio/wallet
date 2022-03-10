# Wallet


## Get Wallet-Key

<small class="badge badge-darkred">requires authentication</small>

Gets the Wallet-Key to grant access to execute transactions.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.shots.com.br/api/wallet/partner/key',
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
    "https://wallet.shots.com.br/api/wallet/partner/key"
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
    "wallet_key": "40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"
}
```
<div id="execution-results-GETapi-wallet--nickname--key" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-wallet--nickname--key"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-wallet--nickname--key"></code></pre>
</div>
<div id="execution-error-GETapi-wallet--nickname--key" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-wallet--nickname--key"></code></pre>
</div>
<form id="form-GETapi-wallet--nickname--key" data-method="GET" data-path="api/wallet/{nickname}/key" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-wallet--nickname--key', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/wallet/{nickname}/key</code></b>
</p>
<p>
<label id="auth-GETapi-wallet--nickname--key" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-wallet--nickname--key" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
<p>
<b><code>nickname</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
<input type="text" name="nickname" data-endpoint="GETapi-wallet--nickname--key" data-component="url"  hidden>
<br>
Nickname of user.
</p>
</form>


## Wallet info

<small class="badge badge-darkred">requires authentication</small>

Gets wallet information of given Wallet-Key

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.shots.com.br/api/wallet/info',
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
    "https://wallet.shots.com.br/api/wallet/info"
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
    "nickname": "partner",
    "email": "partner@shots.com.br",
    "available": true
}
```
<div id="execution-results-GETapi-wallet-info" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-wallet-info"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-wallet-info"></code></pre>
</div>
<div id="execution-error-GETapi-wallet-info" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-wallet-info"></code></pre>
</div>
<form id="form-GETapi-wallet-info" data-method="GET" data-path="api/wallet/info" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-wallet-info', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/wallet/info</code></b>
</p>
<p>
<label id="auth-GETapi-wallet-info" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-wallet-info" data-component="header"></label>
</p>
</form>


## Account balance

<small class="badge badge-darkred">requires authentication</small>

Retrieves total balance available on wallet.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.shots.com.br/api/wallet/balance',
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
    "https://wallet.shots.com.br/api/wallet/balance"
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
    "formatted": "R$ 10.022,00",
    "numeric": 10022,
    "cents": 1002200
}
```
<div id="execution-results-GETapi-wallet-balance" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-wallet-balance"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-wallet-balance"></code></pre>
</div>
<div id="execution-error-GETapi-wallet-balance" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-wallet-balance"></code></pre>
</div>
<form id="form-GETapi-wallet-balance" data-method="GET" data-path="api/wallet/balance" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-wallet-balance', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/wallet/balance</code></b>
</p>
<p>
<label id="auth-GETapi-wallet-balance" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-wallet-balance" data-component="header"></label>
</p>
</form>


## Statement

<small class="badge badge-darkred">requires authentication</small>

Gathers all transaction data upon given period

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.shots.com.br/api/wallet/statement',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
        'query' => [
            'start'=> '2021-01-01',
            'end'=> '2021-01-31',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.shots.com.br/api/wallet/statement"
);

let params = {
    "start": "2021-01-01",
    "end": "2021-01-31",
};
Object.keys(params)
    .forEach(key => url.searchParams.append(key, params[key]));

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
    "transactions": [],
    "period": [
        "2021-01-31T03:00:00.000000Z",
        "2021-01-01T03:00:00.000000Z"
    ]
}
```
<div id="execution-results-GETapi-wallet-statement" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-wallet-statement"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-wallet-statement"></code></pre>
</div>
<div id="execution-error-GETapi-wallet-statement" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-wallet-statement"></code></pre>
</div>
<form id="form-GETapi-wallet-statement" data-method="GET" data-path="api/wallet/statement" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-wallet-statement', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/wallet/statement</code></b>
</p>
<p>
<label id="auth-GETapi-wallet-statement" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-wallet-statement" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>start</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
<input type="text" name="start" data-endpoint="GETapi-wallet-statement" data-component="query"  hidden>
<br>
Start of period on 'Y-m-d' date format.
</p>
<p>
<b><code>end</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
<input type="text" name="end" data-endpoint="GETapi-wallet-statement" data-component="query"  hidden>
<br>
End of period on 'Y-m-d' date format.
</p>
</form>


## Set default tax for Wallet

<small class="badge badge-darkred">requires authentication</small>

Defines a default tax percentage on payments.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.shots.com.br/api/wallet/tax',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
        'query' => [
            'tax'=> '10',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.shots.com.br/api/wallet/tax"
);

let params = {
    "tax": "10",
};
Object.keys(params)
    .forEach(key => url.searchParams.append(key, params[key]));

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


<div id="execution-results-POSTapi-wallet-tax" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet-tax"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet-tax"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet-tax" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet-tax"></code></pre>
</div>
<form id="form-POSTapi-wallet-tax" data-method="POST" data-path="api/wallet/tax" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet-tax', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/tax</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet-tax" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet-tax" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>tax</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="tax" data-endpoint="POSTapi-wallet-tax" data-component="query" required  hidden>
<br>
Amount of tax set as default to be used on transaction payments.
</p>
</form>


## Set default cashback for Wallet

<small class="badge badge-darkred">requires authentication</small>

Defines a default cashback percentage on customer payments.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->post(
    'https://wallet.shots.com.br/api/wallet/cashback',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
            'Wallet-Key' => '40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7',
        ],
        'query' => [
            'cashback'=> '10',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.shots.com.br/api/wallet/cashback"
);

let params = {
    "cashback": "10",
};
Object.keys(params)
    .forEach(key => url.searchParams.append(key, params[key]));

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


<div id="execution-results-POSTapi-wallet-cashback" hidden>
    <blockquote>Received response<span id="execution-response-status-POSTapi-wallet-cashback"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wallet-cashback"></code></pre>
</div>
<div id="execution-error-POSTapi-wallet-cashback" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wallet-cashback"></code></pre>
</div>
<form id="form-POSTapi-wallet-cashback" data-method="POST" data-path="api/wallet/cashback" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json","Wallet-Key":"40533d028657e5cbded2f6a89770855fdf78d24b2f6fb1cf648f15b34ea1bac7"}' onsubmit="event.preventDefault(); executeTryOut('POSTapi-wallet-cashback', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-black">POST</small>
 <b><code>api/wallet/cashback</code></b>
</p>
<p>
<label id="auth-POSTapi-wallet-cashback" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="POSTapi-wallet-cashback" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>cashback</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="cashback" data-endpoint="POSTapi-wallet-cashback" data-component="query" required  hidden>
<br>
Amount of cashback returned by default on customer to partner transaction payments.
</p>
</form>



