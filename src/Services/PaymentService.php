<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Model\Payment;

class PaymentService extends BaseService
{
    /**
     * @param string $transactionId
     *
     * @return ModelInterface|Payment
     */
    public function getByTransactionId(string $transactionId): ModelInterface
    {
        $response = $this->loadModel(Payment::class)->find()->where(['txid' => $transactionId])->execute();
        return $response->getResult()->firstOrFail();
    }

    /**
     * @param string $merchantReferenceId
     *
     * @return ModelInterface|Payment
     */
    public function getByOrderMerchantId(string $merchantReferenceId): ModelInterface
    {
        $response = $this->loadModel(Payment::class)->find()
            ->where(['orderMerchantId' => $merchantReferenceId])
            ->execute();
        return $response->getResult()->firstOrFail();
    }
}
