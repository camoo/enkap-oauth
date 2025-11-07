<?php

declare(strict_types=1);

namespace Enkap\OAuth\Enum;

use Enkap\OAuth\Exception\EnkapException;

enum GrantType: string
{
    public static function fromName(string $name): self
    {
        $name = strtoupper($name);
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }
        throw new EnkapException(sprintf('Invalid grant type name: %s', $name));
    }
    case CLIENT_CREDENTIALS = 'client_credentials';
    case PASSWORD = 'password';
    case REFRESH_TOKEN = 'refresh_token';
    case AUTH_CODE = 'authorization_code';
    case JWT_BEARER = 'urn:ietf:params:oauth:grant-type:jwt-bearer';
    case SAML2_BEARER = 'urn:ietf:params:oauth:grant-type:saml2-bearer';
    case TOKEN_EXCHANGE = 'urn:ietf:params:oauth:grant-type:token-exchange';
    case IWA_NTLM = 'iwa-ntlm';
}
