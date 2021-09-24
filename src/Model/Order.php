<?php
declare(strict_types=1);

namespace Enkap\OAuth\Model;

use DateTimeInterface;
use Enkap\OAuth\Http\Client;
use Enkap\OAuth\Http\ModelResponse;
use Enkap\OAuth\Model\Asset\LineItem;
use Enkap\OAuth\Model\Asset\OID;

/**
 * @property string $currency
 * @property string $customer_name
 * @property string $description
 * @property string $email
 * @property DateTimeInterface $expiry_date
 * @property DateTimeInterface $order_date
 * @property OID $id
 * @property string $lang_key
 * @property string $merchant_reference
 * @property string $opt_ref_one
 * @property string $opt_ref_two
 * @property string $receipt_url
 * @property float $total_amount
 * @property LineItem[] $items
 * @property string $merchant_reference_id
 * @property string $order_transaction_id
 * @property string $redirect_url
 */
class Order extends BaseModel
{
    private const MODEL_NAME = 'Order';
    private $uri = '/api/order';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    /**
     * Get the supported methods.
     */
    public static function getSupportedMethods(): array
    {
        return [
            Client::GET_REQUEST,
            Client::POST_REQUEST,
            Client::DELETE_REQUEST,
        ];
    }

    /**
     * Get the properties of the object.  Indexed by constants
     *  [0] - Mandatory
     *  [1] - Type
     *  [2] - PHP type
     *  [3] - Is an Array
     *  [4] - Saves directly.
     *
     * @return array
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
        return $this->_data['currency'];
    }

    public function setCurrency(string $currency): Order
    {
        $this->propertyUpdated('currency', $currency);
        $this->_data['currency'] = strtoupper($currency);

        return $this;
    }

    public function getCustomerName(): string
    {
        return $this->_data['customerName'];
    }

    public function setCustomerName(string $value): Order
    {
        $this->propertyUpdated('customerName', $value);
        $this->_data['customerName'] = $value;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->_data['description'];
    }

    public function setDescription(string $value): Order
    {
        $this->propertyUpdated('description', $value);
        $this->_data['description'] = $value;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->_data['email'];
    }

    public function setEmail(string $value): Order
    {
        $this->propertyUpdated('email', $value);
        $this->_data['email'] = $value;

        return $this;
    }

    public function getExpiryDate(): DateTimeInterface
    {
        return $this->_data['expiryDate'];
    }

    public function setExpiryDate(DateTimeInterface $value): Order
    {
        $this->propertyUpdated('expiryDate', $value);
        $this->_data['expiryDate'] = $value;

        return $this;
    }

    public function getOrderDate(): DateTimeInterface
    {
        return $this->_data['orderDate'];
    }

    public function setOrderDate(DateTimeInterface $value): Order
    {
        $this->propertyUpdated('orderDate', $value);
        $this->_data['orderDate'] = $value;

        return $this;
    }

    public function getId(): OID
    {
        return $this->_data['id'];
    }

    public function setId(string $value): Order
    {
        $this->propertyUpdated('id', $value);
        $this->_data['id'] = $value;

        return $this;
    }

    public function getLangKey(): string
    {
        return $this->_data['langKey'];
    }

    public function setLangKey(string $value): Order
    {
        $this->propertyUpdated('langKey', $value);
        $this->_data['langKey'] = $value;

        return $this;
    }

    public function setMerchantReference(string $value): Order
    {
        $this->propertyUpdated('merchantReference', $value);
        $this->_data['merchantReference'] = $value;

        return $this;
    }

    public function getOptRefOne(): string
    {
        return $this->_data['optRefOne'];
    }

    public function setOptRefOne(string $value): Order
    {
        $this->propertyUpdated('optRefOne', $value);
        $this->_data['optRefOne'] = $value;

        return $this;
    }

    public function getOptRefTwo(): string
    {
        return $this->_data['optRefTwo'];
    }

    public function setOptRefTwo(string $value): Order
    {
        $this->propertyUpdated('optRefTwo', $value);
        $this->_data['optRefTwo'] = $value;

        return $this;
    }

    public function getTotalAmount(): float
    {
        return $this->_data['totalAmount'];
    }

    public function setTotalAmount(string $value): Order
    {
        $this->propertyUpdated('totalAmount', $value);
        $this->_data['totalAmount'] = $value;

        return $this;
    }

    public function getReceiptUrl(): float
    {
        return $this->_data['receiptUrl'];
    }

    public function setReceiptUrl(string $value): Order
    {
        $this->propertyUpdated('receiptUrl', $value);
        $this->_data['receiptUrl'] = $value;

        return $this;
    }

    /**
     * @return LineItem[]|Collection
     */
    public function getItems()
    {
        if (!isset($this->_data['items'])) {
            $this->_data['items'] = new Collection();
        }
        return $this->_data['items'];
    }

    public function setItems(LineItem $value): Order
    {
        $this->propertyUpdated('items', $value);
        if (!isset($this->_data['items'])) {
            $this->_data['items'] = new Collection();
        }
        $this->_data['items'][] = $value;

        return $this;
    }

    public function getMerchantReferenceId(): string
    {
        return $this->_data['merchantReferenceId'];
    }

    public function getOrderTransactionId(): string
    {
        return $this->_data['orderTransactionId'];
    }

    public function getRedirectUrl(): string
    {
        return $this->_data['redirectUrl'];
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
