<?php

declare(strict_types=1);

namespace Enkap\OAuth\Model;

use Enkap\OAuth\Enum\HttpRequestType;

/**
 * @property string $notification_url
 * @property string $return_url
 */
class CallbackUrl extends BaseModel
{
    private const MODEL_NAME = 'CallbackUrl';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    /** Get the supported methods. */
    public static function getSupportedMethods(): array
    {
        return [
            HttpRequestType::PUT_REQUEST->value,
        ];
    }

    /**
     * Get the properties of the object.  Indexed by constants
     *  [0] - Mandatory
     *  [1] - Type
     *  [2] - PHP type
     *  [3] - Is an Array
     *  [4] - Save directly.
     */
    public static function getProperties(): array
    {
        return [
            'notificationUrl' => [true, self::PROPERTY_TYPE_STRING, null, false, false],
            'returnUrl' => [true, self::PROPERTY_TYPE_STRING, null, false, false],
        ];
    }

    public function getNotificationUrl(): string
    {
        return $this->_data['notificationUrl'];
    }

    public function setNotificationUrl(string $value): self
    {
        $this->propertyUpdated('notificationUrl', $value);
        $this->_data['notificationUrl'] = $value;

        return $this;
    }

    public function getReturnUrl(): string
    {
        return $this->_data['returnUrl'];
    }

    public function setReturnUrl(string $value): self
    {
        $this->propertyUpdated('returnUrl', $value);
        $this->_data['returnUrl'] = $value;

        return $this;
    }

    public function getResourceURI(): string
    {
        return '/api/order/setup';
    }
}
