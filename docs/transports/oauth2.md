# Transport OAuth2

OAuth2 Transport requires a third package: 

```bash
composer require league/oauth2-client
```

Example of use:

```php
<?php
use Cristal\ApiWrapper\Transports\OAuth2;
use Illuminate\Cache\Repository as Cache;
use Curl\Curl;
use League\OAuth2\Client\Provider\GenericProvider;

$provider = new GenericProvider([
    'clientId' => '...',
    'clientSecret' => '...',
    'urlAccessToken' => '...',
    'urlAuthorize' => '...',
    'urlResourceOwnerDetails' => '...',
]);

$transport = new OAuth2('http://...', $provider, new Curl);
$transport
    ->setCacheRepository(new Cache)
    ->setGrant('password')
    ->setOptions([
        'username' => '...',
        'password' => '...',
        'scope' => '...',
    ])
;
```
