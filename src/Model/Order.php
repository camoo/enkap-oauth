<?php

declare(strict_types=1);

namespace Enkap\OAuth\Model;

use DateTimeInterface;
use Enkap\OAuth\Enum\HttpRequestType;
use Enkap\OAuth\Http\ModelResponse;
use Enkap\OAuth\Model\Asset\LineItem;
use Enkap\OAuth\Model\Asset\OID;

/**
 * @property string            $currency
 * @property string            $customer_name
 * @property string            $description
 * @property string            $email
 * @property DateTimeInterface $expiry_date
 * @property DateTimeInterface $order_date
 * @property OID               $id
 * @property string            $lang_key
 * @property string            $merchant_reference
 * @property string            $opt_ref_one
 * @property string            $opt_ref_two
 * @property string            $receipt_url
 * @property float             $total_amount
 * @property LineItem[]        $items
 * @property string            $merchant_reference_id
 * @property string            $order_transaction_id
 * @property string            $redirect_url
 */
class Order extends BaseModel
{
    private const MODEL_NAME = 'Order';

    private string $uri = '/api/order';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    /** Get the supported methods. */
    public static function getSupportedMethods(): array
    {
        return [
            HttpRequestType::GET_REQUEST->value,
            HttpRequestType::POST_REQUEST->value,
            HttpRequestType::DELETE_REQUEST->value,
        ];
    }

    /**
     * Get the properties of the object.  Indexed by constants
     *  [0] - Mandatory
     *  [1] - Type
     *  [2] - PHP type
     *  [3] - Is an Array
     *  [4] - Saves directly.
     */
    public static function getProperties(): array
    {
        return [
            'currency' => [true, self::PROPERTY_TYPE_STRING, null, false, false],
            'customerName' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'description' => [true, self::PROPERTY_TYPE_STRING, null, false, false],
            'email' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'expiryDate' => [false, self::PROPERTY_TYPE_DATE, DateTimeInterface::class, false, false],
            'id' => [false, self::PROPERTY_TYPE_OBJECT, OID::class, false, false],
            'items' => [false, self::PROPERTY_TYPE_OBJECT, LineItem::class, true, false],
            'langKey' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'merchantReference' => [true, self::PROPERTY_TYPE_STRING, null, false, false],
            'optRefOne' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'optRefTwo' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'orderDate' => [false, self::PROPERTY_TYPE_DATE, DateTimeInterface::class, false, false],
            'phoneNumber' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'receiptUrl' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'totalAmount' => [true, self::PROPERTY_TYPE_FLOAT, null, false, false],
            'merchantReferenceId' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'orderTransactionId' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'redirectUrl' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
        ];
    }

    public function getCurrency(): string
    {
        return $this->modelData['currency'];
    }

    public function setCurrency(string $currency): Order
    {
        $this->propertyUpdated('currency', $currency);
        $this->modelData['currency'] = strtoupper($currency);

        return $this;
    }

    public function getCustomerName(): string
    {
        return $this->modelData['customerName'];
    }

    public function setCustomerName(string $value): Order
    {
        $this->propertyUpdated('customerName', $value);
        $this->modelData['customerName'] = $value;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->modelData['description'];
    }

    public function setDescription(string $value): Order
    {
        $this->propertyUpdated('description', $value);
        $this->modelData['description'] = $value;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->modelData['email'];
    }

    public function setEmail(string $value): Order
    {
        $this->propertyUpdated('email', $value);
        $this->modelData['email'] = $value;

        return $this;
    }

    public function getExpiryDate(): DateTimeInterface
    {
        return $this->modelData['expiryDate'];
    }

    public function setExpiryDate(DateTimeInterface $value): Order
    {
        $this->propertyUpdated('expiryDate', $value);
        $this->modelData['expiryDate'] = $value;

        return $this;
    }

    public function getOrderDate(): DateTimeInterface
    {
        return $this->modelData['orderDate'];
    }

    public function setOrderDate(DateTimeInterface $value): Order
    {
        $this->propertyUpdated('orderDate', $value);
        $this->modelData['orderDate'] = $value;

        return $this;
    }

    public function getId(): OID
    {
        return $this->modelData['id'];
    }

    public function setId(string $value): Order
    {
        $this->propertyUpdated('id', $value);
        $this->modelData['id'] = $value;

        return $this;
    }

    public function getLangKey(): string
    {
        return $this->modelData['langKey'];
    }

    public function setLangKey(string $value): Order
    {
        $this->propertyUpdated('langKey', $value);
        $this->modelData['langKey'] = $value;

        return $this;
    }

    public function setMerchantReference(string $value): Order
    {
        $this->propertyUpdated('merchantReference', $value);
        $this->modelData['merchantReference'] = $value;

        return $this;
    }

    public function getOptRefOne(): string
    {
        return $this->modelData['optRefOne'];
    }

    public function setOptRefOne(string $value): Order
    {
        $this->propertyUpdated('optRefOne', $value);
        $this->modelData['optRefOne'] = $value;

        return $this;
    }

    public function getOptRefTwo(): string
    {
        return $this->modelData['optRefTwo'];
    }

    public function setOptRefTwo(string $value): Order
    {
        $this->propertyUpdated('optRefTwo', $value);
        $this->modelData['optRefTwo'] = $value;

        return $this;
    }

    public function getTotalAmount(): float
    {
        return $this->modelData['totalAmount'];
    }

    public function setTotalAmount(string $value): Order
    {
        $this->propertyUpdated('totalAmount', $value);
        $this->modelData['totalAmount'] = $value;

        return $this;
    }

    public function getReceiptUrl(): float
    {
        return $this->modelData['receiptUrl'];
    }

    public function setReceiptUrl(string $value): Order
    {
        $this->propertyUpdated('receiptUrl', $value);
        $this->modelData['receiptUrl'] = $value;

        return $this;
    }

    /** @return LineItem[]|Collection */
    public function getItems(): array|Collection
    {
        if (!isset($this->modelData['items'])) {
            $this->modelData['items'] = new Collection();
        }

        return $this->modelData['items'];
    }

    public function setItems(LineItem $value): Order
    {
        $this->propertyUpdated('items', $value);
        if (!isset($this->modelData['items'])) {
            $this->modelData['items'] = new Collection();
        }
        $this->modelData['items'][] = $value;

        return $this;
    }

    public function getMerchantReferenceId(): string
    {
        return $this->modelData['merchantReferenceId'];
    }

    public function getOrderTransactionId(): string
    {
        return $this->modelData['orderTransactionId'];
    }

    public function getRedirectUrl(): string
    {
        return $this->modelData['redirectUrl'];
    }

    public function getResourceURI(): string
    {
        return $this->uri;
    }

    public function delete(): ModelResponse
    {
        $this->uri .= '/' . $this->getOrderTransactionId();

        return parent::delete();
    }
}
