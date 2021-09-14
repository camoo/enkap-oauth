<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Http\Client;
use Enkap\OAuth\Http\ClientFactory;
use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Model\Order;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

class OrderService extends BaseService
{
    public function place(Order $order): ?Order
    {
        try {
            $collection = $order->save($this->authService);
            /** @var Order $response */
            $response = $collection->firstOrFail();
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
        $order = new Order();
        $response = $this->get($order, ['txid' => $transactionId]);
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
        $order = new Order();
        $response  = $this->get($order, ['orderMerchantId' => $merchantReferenceId]);
        return $response->getResult()->firstOrFail();
    }

    /**
     * @param string $transactionId
     *
     * @return bool
     * @throws GuzzleException
     */
    public function deleteByTransactionId(string $transactionId): bool
    {
        $model = new Order();
        $client = ClientFactory::create();
        $suffix =  $model->getResourceURI().DIRECTORY_SEPARATOR . $transactionId;
        if (!Client::SANDBOX) {
            $suffix = '/v1.2/' . $suffix;
        }
        $uri = sprintf('/purchase%s', $suffix);
        $header = [
            'Authorization' => sprintf('Bearer %s', $this->authService->getAccessToken()),
            'Content-Type' => 'application/json',
        ];
        $response = $client->delete($uri, [], $header);
        return $response->getStatusCode() === self::HTTP_SUCCESS_CODE;
    }
}
