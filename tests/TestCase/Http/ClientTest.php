<?php

namespace Enkap\OAuth\Test\TestCase\Http;

use Enkap\OAuth\Http\Client as HttpClient;
use Enkap\OAuth\Model\Order;
use Enkap\OAuth\Model\Payment;
use Enkap\OAuth\Model\Status;
use Enkap\OAuth\Services\OAuthService;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(
            HttpClient::class,
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
