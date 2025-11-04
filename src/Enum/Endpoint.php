<?php

declare(strict_types=1);

namespace Enkap\OAuth\Enum;

enum Endpoint: string
{
    case ENKAP_API_URL_LIVE = 'api-v2.enkap.cm';

    case ENKAP_API_URL_SANDBOX = 'https://api.enkap-staging.maviance.info';

    case API_VERSION = 'v1.2';
}
