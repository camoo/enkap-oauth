<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Model\Status;
use GuzzleHttp\Exception\GuzzleException;

class StatusService extends BaseService
{

    /**
     * @param string $transactionId
     *
     * @return Status|ModelInterface
     * @throws GuzzleException
     */
    public function getByTransactionId(string $transactionId): Status
    {
        $response = $this->get(new Status(), ['txid' => $transactionId]);
        return $response->getResult()->firstOrFail();
    }

    /**
     * @param string $merchantReferenceId
     *
     * @return Status|ModelInterface
     * @throws GuzzleException
     */
    public function getByOrderMerchantId(string $merchantReferenceId): Status
    {
        $response = $this->get(new Status(), ['orderMerchantId' => $merchantReferenceId]);
        return $response->getResult()->firstOrFail();
    }
}
