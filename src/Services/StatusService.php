<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Model\Status;

class StatusService extends BaseService
{

    /**
     * @param string $transactionId
     *
     * @return Status|ModelInterface
     */
    public function getByTransactionId(string $transactionId): Status
    {
        $status = $this->loadModel(Status::class);
        $response = $status->find()->where(['txid' => $transactionId])->execute();
        return $response->getResult()->firstOrFail();
    }

    /**
     * @param string $merchantReferenceId
     *
     * @return Status|ModelInterface
     */
    public function getByOrderMerchantId(string $merchantReferenceId): Status
    {
        $status = $this->loadModel(Status::class);
        $response = $status->find()->where(['orderMerchantId' => $merchantReferenceId])->execute();
        return $response->getResult()->firstOrFail();
    }
}
