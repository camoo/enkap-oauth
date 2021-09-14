<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Model\Order;
use GuzzleHttp\Exception\GuzzleException;

class StatusService extends BaseService
{
    public const CREATED_STATUS = 'CREATED';
    public const INITIALISED_STATUS = 'INITIALISED';
    public const IN_PROGRESS_STATUS = 'IN_PROGRESS';
    public const CONFIRMED_STATUS = 'CONFIRMED';
    public const FAILED_STATUS = 'FAILED';
    public const CANCELED_STATUS = 'CANCELED';


    /**
     * @param string $transactionId
     *
     * @return string
     * @throws GuzzleException
     */
    public function getByTransactionId(string $transactionId): string
    {
        $order = new Order();
        $uri = $order->getResourceURI() . DIRECTORY_SEPARATOR . 'status';

        $response = $this->get($order, ['txid' => $transactionId], $uri);
        /** @var Order $modelResponse */
        $modelResponse = $response->getResult()->firstOrFail();
        return $modelResponse->getPaymentStatus();
    }

    /**
     * @param string $merchantReferenceId
     *
     * @return string
     * @throws GuzzleException
     */
    public function getByOrderMerchantId(string $merchantReferenceId): string
    {
        $order = new Order();
        $uri = $order->getResourceURI() . DIRECTORY_SEPARATOR . 'status';
        $response = $this->get($order, ['orderMerchantId' => $merchantReferenceId], $uri);
        /** @var Order $modelResponse */
        $modelResponse = $response->getResult()->firstOrFail();
        return $modelResponse->getPaymentStatus();
    }
}
