# enkap-oauth
E-commerce Plugins for SmobilPay

## Installation

```shell
composer require camoo/enkap-oauth
```

## Usage

```php
use Enkap\OAuth\Lib\OAuthService;

$consumerKey = 'hqBvUfOjdLoP04763L_LDO';
$consumerSecret = 'FwxKTJzN4jE8IYdeCM83';

$service = new OAuthService($key, $secret);

var_dump($service->getAccessToken());
```
