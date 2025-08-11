<?php

declare(strict_types=1);

namespace Enkap\OAuth\Http;

use Camoo\Http\Curl\Domain\Client\ClientInterface;
use Camoo\Http\Curl\Domain\Entity\Configuration;
use Camoo\Http\Curl\Infrastructure\Client as CamooClient;
use Camoo\Http\Curl\Infrastructure\Request;
use Enkap\OAuth\Enum\Endpoint;
use Enkap\OAuth\Enum\HttpRequestType;
use Enkap\OAuth\Exception\EnkapBadResponseException;
use Enkap\OAuth\Exception\EnkapException;
use Enkap\OAuth\Exception\EnkapHttpClientException;
use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Lib\Helper;
use Enkap\OAuth\Model\ModelCollection;
use Enkap\OAuth\Services\OAuthService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Throwable;
use Valitron\Validator;

/**
 * Class Client
 */
class Client
{
    private const ENKAP_CLIENT_TIMEOUT = 30;

    private const USER_AGENT_STRING = 'Enkap/CamooClient/%s (+https://github.com/camoo/enkap-oauth)';

    /**
     * @var string[]
     *
     * @see Helper::getPackageVersion()
     */
    protected array $userAgent = [];

    /**
     * @var array<string, null>
     *
     * @see HttpRequestType
     */
    protected array $requestVerbs = [
        HttpRequestType::GET_REQUEST->value => null,
        HttpRequestType::POST_REQUEST->value => null,
        HttpRequestType::PUT_REQUEST->value => null,
        HttpRequestType::DELETE_REQUEST->value => null,
    ];

    private bool $sandbox = false;

    /** Debug switch (default set to false) */
    private bool $debug = false;

    /** Debug file location (log to STDOUT by default) */
    private string $debugFile = 'php://output';

    /** @var string[] */
    private array $headers = [];

    public function __construct(private readonly OAuthService $authService, private ?string $returnType = null)
    {
        $this->addUserAgentString($this->getAPIInfo());
        $this->addUserAgentString(Helper::getPhpVersion());
    }

    public function addUserAgentString(string $userAgent): void
    {
        $this->userAgent[] = $userAgent;
    }

    /**
     * @param array<string, mixed> $data
     * @param string[]             $headers
     */
    public function post(
        string $uri,
        array $data = [],
        array $headers = [],
        ?ClientInterface $client = null
    ): ModelResponse {
        return $this->performRequest(HttpRequestType::POST_REQUEST, $uri, $data, $headers, $client);
    }

    /**
     * @param array<string, mixed> $data
     * @param string[]             $headers
     */
    public function get(
        ModelInterface $model,
        array $data = [],
        ?string $uri = null,
        array $headers = [],
        ?ClientInterface $client = null
    ): ModelResponse {
        $this->returnType = $this->returnType ?? $model->getModelName();
        $suffix = $uri ?? $model->getResourceURI();

        $suffix = DIRECTORY_SEPARATOR . Endpoint::API_VERSION->value . $suffix;

        $uri = sprintf('/purchase%s', $suffix);
        $header = [
            'Authorization' => sprintf('Bearer %s', $this->authService->getAccessToken()),
        ];
        $headers += $header;

        return $this->performRequest(HttpRequestType::GET_REQUEST, $uri, $data, $headers, $client);
    }

    public function save(ModelInterface $model, bool $delete = false, ?ClientInterface $client = null): ModelResponse
    {
        $model->validate();
        $header = [
            'Authorization' => sprintf('Bearer %s', $this->authService->getAccessToken()),
            'Content-Type' => 'application/json',
        ];
        $this->returnType = $this->returnType ?? $model->getModelName();

        if ($delete === true) {
            $method = HttpRequestType::DELETE_REQUEST;
        } else {
            $method = $model->isMethodSupported(HttpRequestType::PUT_REQUEST->value) ?
                HttpRequestType::PUT_REQUEST : HttpRequestType::POST_REQUEST;
        }

        $suffix = $model->getResourceURI();
        $suffix = DIRECTORY_SEPARATOR . Endpoint::API_VERSION->value . $suffix;

        $uri = sprintf('/purchase%s', $suffix);

        if (!$model->isMethodSupported($method->value)) {
            throw new EnkapException(sprintf('%s does not support [%s] via the API', get_class($model), $method->value));
        }
        $data = $model->toStringArray();
        $modelResponse = $this->performRequest($method, $uri, $data, $header, $client);
        $model->setClean();

        return $modelResponse;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    public function setSandbox(bool $sandbox): void
    {
        $this->sandbox = $sandbox;
    }

    public function getSandbox(): bool
    {
        return $this->sandbox;
    }

    /** @return string userAgentString */
    protected function getUserAgentString(): string
    {
        return implode(' ', $this->userAgent);
    }

    /**
     * @param array<string, mixed> $data
     * @param string[]             $headers
     */
    protected function performRequest(
        HttpRequestType $method,
        string $uri,
        array $data = [],
        array $headers = [],
        ?ClientInterface $oClient = null
    ): ModelResponse {
        $this->setHeader($headers);
        //VALIDATE HEADERS
        $hHeaders = $this->getHeaders();
        $sMethod = strtoupper($method->value);

        $mainUrl = $this->sandbox ? Endpoint::ENKAP_API_URL_SANDBOX : Endpoint::ENKAP_API_URL_LIVE;

        $endPoint = $mainUrl->value . $uri;

        $validator = new Validator(array_merge(['request' => $sMethod], $hHeaders));

        $validateRequest = $this->validatorDefault($validator);

        if ($validateRequest === false) {
            throw new EnkapHttpClientException(json_encode($validator->errors()));
        }

        $configuration = new Configuration(self::ENKAP_CLIENT_TIMEOUT);
        $configuration->setDebug($this->getDebug());
        $configuration->setDebugFile($this->debugFile);

        try {
            $client = null === $oClient ? new CamooClient($configuration) : $oClient;

            if ($this->returnType === 'Token') {
                $data = [];
            }

            $request = $this->getRequest($configuration, $sMethod, $endPoint, $data, $hHeaders);
            $requestResponse = $client->sendRequest($request);

            if (!in_array($requestResponse->getStatusCode(), [200, 201], true)) {
                throw new EnkapBadResponseException(
                    (string)$requestResponse->getBody(),
                    $requestResponse->getStatusCode()
                );
            }

            $response = new Response(
                (string)$requestResponse->getBody(),
                $requestResponse->getStatusCode(),
                $requestResponse->getHeaders()
            );

            $data = $method === HttpRequestType::DELETE_REQUEST ? [] : [$response->getJson()];

            return new ModelResponse(
                ModelCollection::create($data, $this->returnType),
                $response->getStatusCode(),
                $response->getHeaders()
            );
        } catch (Throwable $exception) {
            throw new EnkapHttpClientException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }
    }

    /** @param string[] $option */
    protected function setHeader(array $option = []): void
    {
        $this->headers += $option;
    }

    /** @return string[] */
    protected function getHeaders(): array
    {
        $default = [
            'User-Agent' => $this->getUserAgentString(),
        ];

        return $this->headers += $default;
    }

    protected function getAPIInfo(): string
    {
        return sprintf(static::USER_AGENT_STRING, Helper::getPackageVersion());
    }

    /** * Get request instance
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $headers
     */
    protected function getRequest(
        Configuration $configuration,
        string $type,
        string|UriInterface $uri,
        array $data = [],
        array $headers = []
    ): RequestInterface {
        return new Request(
            $configuration,
            $uri,
            $headers,
            $data,
            $type
        );
    }

    /** Validate request params */
    private function validatorDefault(Validator $validator): bool
    {
        $validator->rule('required', ['Authorization']);
        $validator->rule('optional', ['User-Agent']);

        return $validator->rule('in', 'request', array_keys($this->requestVerbs))->validate();
    }

    private function getDebug(): bool
    {
        return $this->debug;
    }
}
