<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Model\Order;

class OrderService extends BaseService
{
    /**
     * @param Order|ModelInterface $order
     * @return Order|null
     */
    public function place(Order $order): ?Order
    {
        $order->setClient($this->client);
        $collection = $order->save();

        return $collection->getResult()->firstOrFail();
    }

    /**
     * @param Order|ModelInterface $order
     * @return bool|null
     */
    public function delete(Order $order): ?bool
    {
        $order->setClient($this->client);
        $response = $order->delete();

        return $response->getStatusCode() === self::HTTP_SUCCESS_CODE;
    }

}
