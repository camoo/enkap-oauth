<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Http\Client;
use Enkap\OAuth\Http\ClientFactory;
use Enkap\OAuth\Http\ModelResponse;
use Enkap\OAuth\Interfaces\ModelInterface;
use GuzzleHttp\Exception\GuzzleException;

class BaseService
{
    protected const HTTP_SUCCESS_CODE = 200;

    /** @var Client $client */
    protected $client;

    public function __construct(string $consumerKey, string $consumerSecret)
    {
        $this->client = ClientFactory::create(new OAuthService($consumerKey, $consumerSecret));
    }

    /**
     * @throws GuzzleException
     */
    public function get(ModelInterface $model, array $data): ModelResponse
    {
        return $this->client->get($model, $data);
    }

}
