<?php

declare(strict_types=1);

namespace Enkap\OAuth\Enum;

enum PaymentStatus: string
{
    /** @return string[] */
    public static function getAllStatuses(): array
    {
        return array_map(fn (PaymentStatus $status) => $status->value, self::cases());
    }

    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::getAllStatuses(), true);
    }

    public function is(self $paymentStatus): bool
    {
        return $this->value === $paymentStatus->value;
    }

    case CREATED_STATUS = 'CREATED';

    case INITIALISED_STATUS = 'INITIALISED';

    case IN_PROGRESS_STATUS = 'IN_PROGRESS';

    case CONFIRMED_STATUS = 'CONFIRMED';

    case FAILED_STATUS = 'FAILED';

    case CANCELED_STATUS = 'CANCELED';

    case REFUNDED_STATUS = 'REFUNDED';

    case EXPIRED_STATUS = 'EXPIRED';
    case UNKNOWN_STATUS = 'UNKNOWN';
}
