<?php

declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Model\Order;

class OrderService extends BaseService
{
    public function place(Order $order): ModelInterface
    {
        $order->setClient($this->client);
        $collection = $order->save();

        return $collection->getResult()->firstOrFail();
    }

    public function delete(Order $order): ?bool
    {
        $order->setClient($this->client);
        $response = $order->delete();

        return $response->getStatusCode() === self::HTTP_SUCCESS_CODE;
    }
}
