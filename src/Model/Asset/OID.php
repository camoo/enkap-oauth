<?php
declare(strict_types=1);

namespace Enkap\OAuth\Model\Asset;

use Enkap\OAuth\Model\BaseModel;

/**
 * @property string $uuid
 * @property string $version
 */
class OID extends BaseModel
{
    private const MODEL_NAME = 'OID';
    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    /**
     * Get the supported methods.
     */
    public static function getSupportedMethods(): array
    {
        return [];
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
            'uuid' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
            'version' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
        ];
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->_data['uuid'];
    }

    /**
     * @param string $value
     *
     * @return OID
     */
    public function setUuid(string $value): self
    {
        $this->propertyUpdated('uuid', $value);
        $this->_data['uuid'] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->_data['version'];
    }

    /**
     * @param string $value
     *
     * @return OID
     */
    public function setVersion(string $value): self
    {
        $this->propertyUpdated('version', $value);
        $this->_data['version'] = $value;

        return $this;
    }

    public function getResourceURI(): string
    {
        return '';
    }
}
