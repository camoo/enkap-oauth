<?php
declare(strict_types=1);

namespace Enkap\OAuth\Model;

use DateTimeInterface;
use Enkap\OAuth\Http\Client;
use Enkap\OAuth\Model\Asset\OID;

/**
 * @property string $payment_status
 * @property string $payer_account_name
 * @property string $payer_account_number
 * @property string $payment_provider_id
 * @property string $payment_provider_name
 * @property OID $id
 * @property DateTimeInterface $payment_date
 * @property DateTimeInterface $order_date
 * @property Order $order
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
            Client::GET_REQUEST,
        ];
    }

    public function getPayerAccountName(): string
    {
        return $this->_data['payerAccountName'];
    }

    public function getPaymentProviderName(): string
    {
        return $this->_data['paymentProviderName'];
    }

    public function getPaymentProviderId(): string
    {
        return $this->_data['paymentProviderId'];
    }

    public function getPayerAccountNumber(): string
    {
        return $this->_data['payerAccountNumber'];
    }

    public function getId(): OID
    {
        return $this->_data['id'];
    }

    public function getOrder(): ?Order
    {
        return $this->_data['order'];
    }

    public function getPaymentStatus(): string
    {
        return $this->_data['paymentStatus'];
    }

    public function getOrderDate(): DateTimeInterface
    {
        return $this->_data['orderDate'];
    }

    public function getPaymentDate(): DateTimeInterface
    {
        return $this->_data['paymentDate'];
    }
}
