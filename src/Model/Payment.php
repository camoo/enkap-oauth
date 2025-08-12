<?php

declare(strict_types=1);

namespace Enkap\OAuth\Model;

use DateTimeInterface;
use Enkap\OAuth\Enum\HttpRequestType;
use Enkap\OAuth\Model\Asset\OID;

/**
 * @property string            $payment_status
 * @property string            $payer_account_name
 * @property string            $payer_account_number
 * @property string            $payment_provider_id
 * @property string            $payment_provider_name
 * @property OID               $id
 * @property DateTimeInterface $payment_date
 * @property DateTimeInterface $order_date
 * @property Order             $order
 */
class Payment extends BaseModel
{
    private const MODEL_NAME = 'Payment';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    public function getResourceURI(): string
    {
        return '/api/order';
    }

    public static function getProperties(): array
    {
        return [
            'paymentStatus' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'payerAccountName' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'payerAccountNumber' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'paymentProviderId' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'paymentProviderName' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'orderDate' => [false, self::PROPERTY_TYPE_DATE, DateTimeInterface::class, false, false],
            'paymentDate' => [false, self::PROPERTY_TYPE_DATE, DateTimeInterface::class, false, false],
            'id' => [false, self::PROPERTY_TYPE_OBJECT, OID::class, false, false],
            'order' => [false, self::PROPERTY_TYPE_OBJECT, Order::class, false, false],
        ];
    }

    public static function getSupportedMethods(): array
    {
        return [
            HttpRequestType::GET_REQUEST->value,
        ];
    }

    public function getPayerAccountName(): string
    {
        return $this->modelData['payerAccountName'];
    }

    public function getPaymentProviderName(): string
    {
        return $this->modelData['paymentProviderName'];
    }

    public function getPaymentProviderId(): string
    {
        return $this->modelData['paymentProviderId'];
    }

    public function getPayerAccountNumber(): string
    {
        return $this->modelData['payerAccountNumber'];
    }

    public function getId(): OID
    {
        return $this->modelData['id'];
    }

    public function getOrder(): ?Order
    {
        return $this->modelData['order'];
    }

    public function getPaymentStatus(): string
    {
        return $this->modelData['paymentStatus'];
    }

    public function getOrderDate(): DateTimeInterface
    {
        return $this->modelData['orderDate'];
    }

    public function getPaymentDate(): DateTimeInterface
    {
        return $this->modelData['paymentDate'];
    }
}
