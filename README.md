# enkap-oauth

E-commerce Plugins for SmobilPay

## Installation

```shell
composer require camoo/enkap-oauth
```

## Usage

```php
use Enkap\OAuth\Services\OAuthService;

$consumerKey = 'hqBvUfOjdLoP04763L_LDO';
$consumerSecret = 'FwxKTJzN4jE8IYdeCM83';

$service = new OAuthService($key, $secret);

var_dump($service->getAccessToken());
```

## Initiate payment

```php
use Enkap\OAuth\Services\OAuthService;
use Enkap\OAuth\Model\Order;
use Enkap\OAuth\Services\OrderService;
use Enkap\OAuth\Lib\Helper;

$consumerKey = 'hqBvUfOjdLoP04763L_LDO';
$consumerSecret = 'FwxKTJzN4jE8IYdeCM83';

$orderService = new OrderService($key, $secret);
$order = new Order();
$dataData = [
    'merchantReference' => uniqid('', true),
    'email' => 'enkap@mail.tld',
    'customerName' => 'My customer',
    'totalAmount' => 6400,
    'description' => 'Camoo Test Payment',
    'currency' => 'XAF',
    'items' => [
        [
            'itemId' => '1',
            'particulars' => 'soya bien pimenté',
            'unitCost' => 100,
            'quantity' => 50,
        ],
        [
            'itemId' => 2,
            'unitCost' => 700,
            'quantity' => 2,
            'particulars' => 'Bière 33 Export',
        ]
    ]
];
$order->fromStringArray($dataData);

try {
    $collection = $order->save($authService);
    /** @var Order $response */
    $response = $collection->firstOrFail();
     
     // Save references into your Database
     $entity = $this->Payments->newEntity($dataData);
     $entity->set('oder_transaction_id', $response->getOrderTransactionId());
     $this->Payments->save($entity);

     // redirect User to Enkap System
     Helper::redirect($response->getRedirectUrl());
     
} catch (\Throwable $e) {
    var_dump($e->getMessage());
}
```

## Get Order

```php
use Enkap\OAuth\Services\OrderService;
use Enkap\OAuth\Model\Order;

$consumerKey = 'hqBvUfOjdLoP04763L_LDO';
$consumerSecret = 'FwxKTJzN4jE8IYdeCM83';

$trxId = 'e07355446e0140ea9876a6ba38b155f3';
$orderService = new OrderService($key, $secret);
$orderModel = $orderService->getByTransactionId($trxId);
// status
var_dump($orderModel->getPaymentStatus());

# OR
$internalTrxId = '61405dc1a38878.58742206';
$orderService = new OrderService($key, $secret);
$orderModel = $orderService->getByOrderMerchantId($internalTrxId);

// status
var_dump($orderModel->getPaymentStatus());
```

## Check Payment Status

```php
use Enkap\OAuth\Services\StatusService;
use Enkap\OAuth\Lib\Helper;
use Enkap\OAuth\Model\Status;

$consumerKey = 'hqBvUfOjdLoP04763L_LDO';
$consumerSecret = 'FwxKTJzN4jE8IYdeCM83';

$trxId = 'e07355446e0140ea9876a6ba38b155f3';
$statusService = new StatusService($key, $secret);
$status = $statusService->getByTransactionId($trxId);
// Update your database
$query = $this->Payments->query()->set(['status' => $status->getCurrent()])->where(['oder_transaction_id' => $trxId]);
if ($status->confirmed()){
    // Payment successfully completed
    // send Item to user/customer
    return;
}

if ($status->failed() || $status->canceled()) {
  // delete that reference from your Database
}
```

## Set Callback Urls to receive Payment status automatically

```php
use Enkap\OAuth\Services\CallbackUrlService;
use Enkap\OAuth\Model\CallbackUrl;

$setup = new CallbackUrlService($key, $secret);
$callBack = new CallbackUrl();
# The URL where to redirect the user after the payment is completed. It will contain the reference id generated by your system which was provided in the initial order placement request. E-nkap will append your reference id in the path of the URL with the form: http://localhost/action/return/{yourReferenceId}
$callBack->return_url = 'http://localhost/action/return';

# The URL used by E-nkap to instantly notify you about the status of the payment. E-nkap would append your reference Id (generated by your system and provided in the initial order placement request) as path variable and send a PUT with the status of the payment in the body as {"status":"[txStatus]"}, where [txStatus] the payment status.
$callBack->notification_url = 'http://localhost/action/notify'; // this action should accept PUT Request
$setup->set($callBack);
```

## Delete Order

```php
use Enkap\OAuth\Services\OrderService;
use Enkap\OAuth\Model\Order;

$consumerKey = 'hqBvUfOjdLoP04763L_LDO';
$consumerSecret = 'FwxKTJzN4jE8IYdeCM83';

$trxId = 'e07355446e0140ea9876a6ba38b155f3';
$orderService = new OrderService($key, $secret);
$orderModel = new Order();
$orderModel->order_transaction_id = $trxId;
$result = $orderService->delete($orderModel);
if ($result === true) {
  // order has been deleted
  //...
}
```
