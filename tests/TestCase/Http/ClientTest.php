<?php

namespace Enkap\OAuth\Test\TestCase\Http;

use Enkap\OAuth\Http\ModelResponse;
use Enkap\OAuth\Model\ModelCollection;
use Enkap\OAuth\Model\Order;
use Enkap\OAuth\Model\Payment;
use Enkap\OAuth\Model\Status;
use Enkap\OAuth\Services\BaseService;
use Enkap\OAuth\Services\OAuthService;
use Enkap\OAuth\Services\OrderService;
use Enkap\OAuth\Services\StatusService;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Enkap\OAuth\Http\Client as HttpClient;

class ClientTest extends TestCase
{

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(HttpClient::class,
            new HttpClient(new OAuthService('eeee', 'yyyy'))
        );
    }

    public function provideClientDependency(): array
    {
        return [
            [Order::class, 'Order', uniqid('', true)],
            [Status::class, 'Status', uniqid('', true)],
            [Payment::class, 'Payment', uniqid('', true)],
        ];
    }

}
