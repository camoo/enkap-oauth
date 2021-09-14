<?php
declare(strict_types=1);

namespace Enkap\OAuth\Http;

class ClientFactory
{

    /**
     * Avoid new instance
     */
    private function __construct()
    {
    }

    public static function create(?string $model = null, ?int $timeout = null): Client
    {
        return new Client($model, $timeout);
    }
}
