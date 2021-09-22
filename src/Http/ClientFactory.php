<?php
declare(strict_types=1);

namespace Enkap\OAuth\Http;

use Enkap\OAuth\Services\OAuthService;

class ClientFactory
{

    /**
     * Avoid new instance
     */
    private function __construct()
    {
    }

    public static function create(OAuthService $authService, array $options = [], ?string $returnType = null): Client
    {
        return new Client($authService, $options, $returnType);
    }
}
