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
 * @property float $sub_total
 */
class LineItem extends BaseModel
{
    private const MODEL_NAME = 'LineItem';

    /** Get the supported methods. */
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

    public function getDescription(): string
    {
        return $this->modelData['particulars'];
    }

    public function setDescription(string $value): LineItem
    {
        $this->propertyUpdated('particulars', $value);
        $this->modelData['particulars'] = $value;

        return $this;
    }

    public function getQuantity(): string
    {
        return $this->modelData['quantity'];
    }

    public function setQuantity(string $value): LineItem
    {
        $this->propertyUpdated('quantity', $value);
        $this->modelData['quantity'] = $value;

        return $this;
    }

    public function getUnitCost(): float
    {
        return $this->modelData['unitCost'];
    }

    public function setUnitCost(float $value): LineItem
    {
        $this->propertyUpdated('unitCost', $value);
        $this->modelData['unitCost'] = $value;

        return $this;
    }

    public function getItemId(): string
    {
        return $this->modelData['itemId'];
    }

    public function setItemId(string $value): LineItem
    {
        $this->propertyUpdated('itemId', $value);
        $this->modelData['itemId'] = $value;

        return $this;
    }

    public function getSubTotal(): float
    {
        return $this->modelData['subTotal'];
    }

    public function setSubTotal(float $value): LineItem
    {
        $this->propertyUpdated('subTotal', $value);
        $this->modelData['subTotal'] = $value;

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
