<?php
declare(strict_types=1);

namespace Enkap\OAuth\Model\Asset;

use Enkap\OAuth\Model\BaseModel;

/**
 * @property string $item_id
 */

/**
 * Description needs to be at least 1 char long. A line item with just a description (i.e no unit
 * amount or quantity) can be created by specifying just a <Description> element that
 * contains at least 1 character.
 *
 * @property string $description
 */

/**
 * LineItem Quantity (max length = 13).
 *
 * @property int $quantity
 */

/**
 * LineItem unit amount. By default, unit amount will be rounded to two decimal places. You can opt in
 *
 * @property float $unit_cost
 */

/**
 *
 * @property float $sub_total
 */
class LineItem extends BaseModel
{
    private const MODEL_NAME = 'LineItem';

    /**
     * Get the supported methods.
     */
    public static function getSupportedMethods(): array
    {
        return [
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
            'itemId' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'particulars' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'quantity' => [false, self::PROPERTY_TYPE_INT, null, false, false],
            'unitCost' => [false, self::PROPERTY_TYPE_FLOAT, null, false, false],
            'subTotal' => [false, self::PROPERTY_TYPE_FLOAT, null, false, false],
        ];
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->_data['particulars'];
    }

    /**
     * @param string $value
     *
     * @return LineItem
     */
    public function setDescription(string $value): LineItem
    {
        $this->propertyUpdated('particulars', $value);
        $this->_data['particulars'] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->_data['quantity'];
    }

    /**
     * @param string $value
     *
     * @return LineItem
     */
    public function setQuantity(string $value): LineItem
    {
        $this->propertyUpdated('quantity', $value);
        $this->_data['quantity'] = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getUnitCost(): float
    {
        return $this->_data['unitCost'];
    }

    /**
     * @param float $value
     *
     * @return LineItem
     */
    public function setUnitCost(float $value): LineItem
    {
        $this->propertyUpdated('unitCost', $value);
        $this->_data['unitCost'] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemId(): string
    {
        return $this->_data['itemId'];
    }

    /**
     * @param string $value
     *
     * @return LineItem
     */
    public function setItemId(string $value): LineItem
    {
        $this->propertyUpdated('itemId', $value);
        $this->_data['itemId'] = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getSubTotal(): float
    {
        return $this->_data['subTotal'];
    }

    /**
     * @param float $value
     *
     * @return LineItem
     */
    public function setSubTotal(float $value): LineItem
    {
        $this->propertyUpdated('subTotal', $value);
        $this->_data['subTotal'] = $value;

        return $this;
    }

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    public function getResourceURI(): string
    {
        return '';
    }
}
