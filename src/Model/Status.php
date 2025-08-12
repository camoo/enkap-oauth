<?php

declare(strict_types=1);

namespace Enkap\OAuth\Model;

use Enkap\OAuth\Enum\HttpRequestType;
use Enkap\OAuth\Enum\PaymentStatus;

class Status extends BaseModel
{
    private const MODEL_NAME = 'Status';

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

    public function getResourceURI(): string
    {
        return '/api/order/status';
    }

    /** @return array<string, mixed> */
    public static function getProperties(): array
    {
        return ['status' => [false, self::PROPERTY_TYPE_STRING, null, false, false]];
    }

    public function getCurrent(): PaymentStatus
    {
        $status = $this->modelData['status'] ?? null;

        if ($status === null || !PaymentStatus::isValidStatus($status)) {
            error_log(sprintf('[Status] Invalid ENKAP status value encountered: "%s"', $status));

            return PaymentStatus::UNKNOWN_STATUS;
        }

        return PaymentStatus::from($status);
    }

    public function initialized(): bool
    {
        return $this->getCurrent()->is(PaymentStatus::INITIALISED_STATUS);
    }

    public function confirmed(): bool
    {
        return $this->getCurrent()->is(PaymentStatus::CONFIRMED_STATUS);
    }

    public function canceled(): bool
    {
        return $this->getCurrent()->is(PaymentStatus::CANCELED_STATUS);
    }

    public function failed(): bool
    {
        return $this->getCurrent()->is(PaymentStatus::FAILED_STATUS);
    }

    public function created(): bool
    {
        return $this->getCurrent()->is(PaymentStatus::CREATED_STATUS);
    }

    public function isInProgress(): bool
    {
        return $this->getCurrent()->is(PaymentStatus::IN_PROGRESS_STATUS);
    }

    public static function getSupportedMethods(): array
    {
        return [HttpRequestType::GET_REQUEST->value];
    }
}
