<?php

declare(strict_types=1);

namespace Enkap\OAuth\Enum;

enum HttpRequestType: string
{
    case GET_REQUEST = 'GET';

    case POST_REQUEST = 'POST';

    case PUT_REQUEST = 'PUT';

    case DELETE_REQUEST = 'DELETE';

}
