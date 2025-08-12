<?php

declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Exception\EnkapException;
use Enkap\OAuth\Http\Client;
use Enkap\OAuth\Http\ClientFactory;
use Enkap\OAuth\Interfaces\ModelInterface;

abstract class BaseService
{
    protected Client $client;

    public function __construct(
        string $consumerKey,
        string $consumerSecret,
        bool $sandbox = false,
        bool $clientDebug = false
    ) {
        $this->client = ClientFactory::create(new OAuthService($consumerKey, $consumerSecret, $sandbox));
        $this->client->setSandbox($sandbox);
        $this->client->setDebug($clientDebug);
    }

    public function loadModel(string $modelName): ModelInterface
    {
        if (!class_exists($modelName)) {
            throw new EnkapException(sprintf('Model %s cannot be loaded', $modelName));
        }

        return new $modelName($this->client);
    }
}
