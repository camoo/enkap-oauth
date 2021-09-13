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

    public function create(int $timeOut = 0): Client
    {
        return new Client($timeOut);
    }
}
