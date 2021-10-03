<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Exception\EnkapException;
use Enkap\OAuth\Http\Client;
use Enkap\OAuth\Http\ClientFactory;
use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Model\BaseModel;

class BaseService
{
    protected const HTTP_SUCCESS_CODE = 200;

    /** @var Client $client */
    protected $client;

    public function __construct(
        string $consumerKey,
        string $consumerSecret,
        array $clientOptions = [],
        bool $sandbox = false,
        bool $clientDebug = false
    ) {
        $this->client = ClientFactory::create(
            new OAuthService($consumerKey, $consumerSecret, $clientOptions, $sandbox),
            $clientOptions
        );
        $this->client->sandbox = $sandbox;
        $this->client->debug = $clientDebug;
    }

    /**
     * @param string $modelName
     * @return ModelInterface|BaseModel
     */
    public function loadModel(string $modelName): ModelInterface
    {
        if (!class_exists($modelName)) {
            throw new EnkapException(sprintf('Model %s cannot be loaded', $modelName));
        }
        return new $modelName($this->client);
    }
}
