# Users


## Check nickname

<small class="badge badge-darkred">requires authentication</small>

Checks if a given nickname is valid and if it's available or not.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/nickname',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
        'query' => [
            'nickname'=> 'sint',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/nickname"
);

let params = {
    "nickname": "sint",
};
Object.keys(params)
    .forEach(key => url.searchParams.append(key, params[key]));

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
    "nickname": "sint",
    "valid": true,
    "errors": [],
    "available": true
}
```
<div id="execution-results-GETapi-nickname" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-nickname"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-nickname"></code></pre>
</div>
<div id="execution-error-GETapi-nickname" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-nickname"></code></pre>
</div>
<form id="form-GETapi-nickname" data-method="GET" data-path="api/nickname" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-nickname', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/nickname</code></b>
</p>
<p>
<label id="auth-GETapi-nickname" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-nickname" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>nickname</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="nickname" data-endpoint="GETapi-nickname" data-component="query" required  hidden>
<br>
Nickname to be checked.
</p>
</form>


## List all available users

<small class="badge badge-darkred">requires authentication</small>

Gets all users registered and available.

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/users/available',
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
    "https://wallet.lifepet.com.br/api/users/available"
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
    "lifepet",
    "customer",
    "partner",
    "staging"
]
```
<div id="execution-results-GETapi-users-available" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-users-available"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-users-available"></code></pre>
</div>
<div id="execution-error-GETapi-users-available" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-users-available"></code></pre>
</div>
<form id="form-GETapi-users-available" data-method="GET" data-path="api/users/available" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-users-available', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/users/available</code></b>
</p>
<p>
<label id="auth-GETapi-users-available" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-users-available" data-component="header"></label>
</p>
</form>


## Find user by nickname

<small class="badge badge-darkred">requires authentication</small>

Finds user with a given nickname

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/users/nickname',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
        'query' => [
            'nickname'=> 'partner',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/users/nickname"
);

let params = {
    "nickname": "partner",
};
Object.keys(params)
    .forEach(key => url.searchParams.append(key, params[key]));

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
    "name": "Partner Simulation",
    "email": "partner@lifepet.com.br",
    "nickname": "partner",
    "type": "BUSINESS"
}
```
<div id="execution-results-GETapi-users-nickname" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-users-nickname"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-users-nickname"></code></pre>
</div>
<div id="execution-error-GETapi-users-nickname" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-users-nickname"></code></pre>
</div>
<form id="form-GETapi-users-nickname" data-method="GET" data-path="api/users/nickname" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-users-nickname', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/users/nickname</code></b>
</p>
<p>
<label id="auth-GETapi-users-nickname" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-users-nickname" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>nickname</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="nickname" data-endpoint="GETapi-users-nickname" data-component="query" required  hidden>
<br>
Nickname of user.
</p>
</form>


## Paginated user search

<small class="badge badge-darkred">requires authentication</small>

Gets a paginated user list filtering by a given term.
The term is used to compare:
- Nickname
- Email
- Name

> Example request:

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'https://wallet.lifepet.com.br/api/users',
    [
        'headers' => [
            'Heimdall-Key' => '{HEIMDALL_KEY}',
            'Accept' => 'application/json',
        ],
        'query' => [
            'page'=> '1',
            'term'=> 'lifepet',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```

```javascript
const url = new URL(
    "https://wallet.lifepet.com.br/api/users"
);

let params = {
    "page": "1",
    "term": "lifepet",
};
Object.keys(params)
    .forEach(key => url.searchParams.append(key, params[key]));

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
        "name": "Lifepet Saúde",
        "email": "lifepet@lifepet.com.br",
        "nickname": "lifepet",
        "type": "BUSINESS"
    },
    {
        "name": "Customer Simulation",
        "email": "customer@lifepet.com.br",
        "nickname": "customer",
        "type": "PERSONAL"
    },
    {
        "name": "Partner Simulation",
        "email": "partner@lifepet.com.br",
        "nickname": "partner",
        "type": "BUSINESS"
    },
    {
        "name": "Simulação de Parceiro",
        "email": "staging@staging.lifepet.com.br",
        "nickname": "staging",
        "type": "BUSINESS"
    }
]
```
<div id="execution-results-GETapi-users" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-users"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-users"></code></pre>
</div>
<div id="execution-error-GETapi-users" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-users"></code></pre>
</div>
<form id="form-GETapi-users" data-method="GET" data-path="api/users" data-authed="1" data-hasfiles="0" data-headers='{"Heimdall-Key":"{HEIMDALL_KEY}","Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-users', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/users</code></b>
</p>
<p>
<label id="auth-GETapi-users" hidden>Heimdall-Key header: <b><code></code></b><input type="text" name="Heimdall-Key" data-prefix="" data-endpoint="GETapi-users" data-component="header"></label>
</p>
<h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
<p>
<b><code>page</code></b>&nbsp;&nbsp;<small>integer</small>  &nbsp;
<input type="number" name="page" data-endpoint="GETapi-users" data-component="query" required  hidden>
<br>
Which page to show.
</p>
<p>
<b><code>term</code></b>&nbsp;&nbsp;<small>string</small>     <i>optional</i> &nbsp;
<input type="text" name="term" data-endpoint="GETapi-users" data-component="query"  hidden>
<br>
Term to compare
</p>
</form>



