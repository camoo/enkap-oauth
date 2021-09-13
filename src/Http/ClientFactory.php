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

    public function create(?string $model = null, int $timeOut = 0): Client
    {
        return new Client($model, $timeOut);
    }
}
