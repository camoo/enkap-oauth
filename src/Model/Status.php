<?php
declare(strict_types=1);

namespace Enkap\OAuth\Model;

use Enkap\OAuth\Http\Client;

class Status extends BaseModel
{
    public const CREATED_STATUS = 'CREATED';
    public const INITIALISED_STATUS = 'INITIALISED';
    public const IN_PROGRESS_STATUS = 'IN_PROGRESS';
    public const CONFIRMED_STATUS = 'CONFIRMED';
    public const FAILED_STATUS = 'FAILED';
    public const CANCELED_STATUS = 'CANCELED';


    private const MODEL_NAME = 'Status';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    public function getResourceURI(): string
    {
        return '/api/order/status';
    }

    public static function getProperties(): array
    {
        return [
            'status' => [false, self::PROPERTY_TYPE_STRING, null, false, false],
        ];
    }

    public static function getAllowedStatus(): array
    {
        return [
            self::CREATED_STATUS,
            self::INITIALISED_STATUS,
            self::IN_PROGRESS_STATUS,
            self::CONFIRMED_STATUS,
            self::FAILED_STATUS,
            self::CANCELED_STATUS
        ];
    }

    public function getCurrent(): string
    {
        return $this->_data['status'];
    }

    public function initialized(): bool
    {
        return $this->getCurrent() === self::INITIALISED_STATUS;
    }

    public function confirmed(): bool
    {
        return $this->getCurrent() === self::CONFIRMED_STATUS;
    }

    public function canceled(): bool
    {
        return $this->getCurrent() === self::CANCELED_STATUS;
    }

    public function failed(): bool
    {
        return $this->getCurrent() === self::FAILED_STATUS;
    }

    public function created(): bool
    {
        return $this->getCurrent() === self::CREATED_STATUS;
    }

    public function isInProgress(): bool
    {
        return $this->getCurrent() === self::IN_PROGRESS_STATUS;
    }

    public static function getSupportedMethods(): array
    {
        return [
            Client::GET_REQUEST
        ];
    }
}
