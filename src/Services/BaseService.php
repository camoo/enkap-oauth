<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Http\ClientFactory;
use Enkap\OAuth\Http\ModelResponse;
use Enkap\OAuth\Interfaces\ModelInterface;
use GuzzleHttp\Exception\GuzzleException;

class BaseService
{
    protected const HTTP_SUCCESS_CODE = 200;
    /**
     * @var OAuthService
     */
    protected $authService;

    public function __construct(string $consumerKey, string $consumerSecret)
    {
        $this->authService = new OAuthService($consumerKey, $consumerSecret);
    }

    /**
     * @throws GuzzleException
     */
    public function get(ModelInterface $model, array $data): ModelResponse
    {
        $client = ClientFactory::create($this->authService);
        return $client->get($model, $data);
    }

}
