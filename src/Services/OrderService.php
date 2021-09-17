<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Model\Order;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

class OrderService extends BaseService
{
    public function place(Order $order): ?Order
    {
        try {
            $collection = $order->save();
            /** @var Order $response */
            $response = $collection->getResult()->firstOrFail();
        } catch (Throwable $e) {
            return null;
        }
        return $response;
    }

    /**
     * @param string $transactionId
     *
     * @return ModelInterface|Order
     * @throws GuzzleException
     */
    public function getByTransactionId(string $transactionId): ModelInterface
    {
        $response = $this->get(new Order(), ['txid' => $transactionId]);
        return $response->getResult()->firstOrFail();
    }

    /**
     * @param string $merchantReferenceId
     *
     * @return ModelInterface|Order
     * @throws GuzzleException
     */
    public function getByOrderMerchantId(string $merchantReferenceId): ModelInterface
    {
        $response = $this->get(new Order(), ['orderMerchantId' => $merchantReferenceId]);
        return $response->getResult()->firstOrFail();
    }

    /**
     * @param Order $order
     * @return bool|null
     */
    public function delete(Order $order): ?bool
    {

        try {
            $response = $order->delete($this->authService);

        } catch (Throwable $e) {
            return null;
        }

        return $response->getStatusCode() === self::HTTP_SUCCESS_CODE;
    }
}
