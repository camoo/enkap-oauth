<?php

declare(strict_types=1);

namespace Enkap\OAuth\Http;

use Camoo\Http\Curl\Domain\Client\ClientInterface;
use Camoo\Http\Curl\Domain\Entity\Configuration;
use Camoo\Http\Curl\Infrastructure\Client as CamooClient;
use Camoo\Http\Curl\Infrastructure\Request;
use Enkap\OAuth\Exception\EnkapBadResponseException;
use Enkap\OAuth\Exception\EnkapException;
use Enkap\OAuth\Exception\EnkapHttpClientException;
use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Lib\Helper;
use Enkap\OAuth\Model\ModelCollection;
use Enkap\OAuth\Services\OAuthService;
use Psr\Http\Message\RequestInterface;
use Throwable;
use Valitron\Validator;

/**
 * Class Client
 */
class Client
{
    public const GET_REQUEST = 'GET';

    public const POST_REQUEST = 'POST';

    public const PUT_REQUEST = 'PUT';

    public const DELETE_REQUEST = 'DELETE';

    private const ENKAP_API_URL_LIVE = 'https://api.enkap.cm';

    private const ENKAP_API_URL_SANDBOX = 'https://api.enkap.maviance.info';

    private const ENKAP_CLIENT_TIMEOUT = 30;

    private const USER_AGENT_STRING = 'Enkap/CamooClient/%s (+https://github.com/camoo/enkap-oauth)';

    private const API_VERSION = 'v1.2';

    public bool $sandbox = false;

    /** Debug switch (default set to false) */
    public bool $debug = false;

    /** Debug file location (log to STDOUT by default) */
    public string $debugFile = 'php://output';

    protected array $userAgent = [];

    protected array $requestVerbs = [
        self::GET_REQUEST => null,
        self::POST_REQUEST => null,
        self::PUT_REQUEST => null,
        self::DELETE_REQUEST => null,
    ];

    private ?string $returnType;

    private array $headers = [];

    private OAuthService $authService;

    public function __construct(OAuthService $authService, ?string $returnType = null)
    {
        $this->addUserAgentString($this->getAPIInfo());
        $this->addUserAgentString(Helper::getPhpVersion());
        $this->returnType = $returnType;
        $this->authService = $authService;
    }

    public function addUserAgentString(string $userAgent): void
    {
        $this->userAgent[] = $userAgent;
    }

    public function post(
        string $uri,
        array $data = [],
        array $headers = [],
        ?ClientInterface $client = null
    ): ModelResponse {
        return $this->performRequest(self::POST_REQUEST, $uri, $data, $headers, $client);
    }

    public function get(
        ModelInterface $model,
        array $data = [],
        ?string $uri = null,
        array $headers = [],
        ?ClientInterface $client = null
    ): ModelResponse {
        $this->returnType = $this->returnType ?? $model->getModelName();
        $suffix = $uri ?? $model->getResourceURI();

        $suffix = DIRECTORY_SEPARATOR . self::API_VERSION . $suffix;

        $uri = sprintf('/purchase%s', $suffix);
        $header = [
            'Authorization' => sprintf('Bearer %s', $this->authService->getAccessToken()),
        ];
        $headers += $header;

        return $this->performRequest(self::GET_REQUEST, $uri, $data, $headers, $client);
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
            $method = self::DELETE_REQUEST;
        } else {
            $method = $model->isMethodSupported(self::PUT_REQUEST) ? self::PUT_REQUEST : self::POST_REQUEST;
        }

        $suffix = $model->getResourceURI();
        $suffix = DIRECTORY_SEPARATOR . self::API_VERSION . $suffix;

        $uri = sprintf('/purchase%s', $suffix);

        if (!$model->isMethodSupported($method)) {
            throw new EnkapException(sprintf('%s does not support [%s] via the API', get_class($model), $method));
        }
        $data = $model->toStringArray();
        $modelResponse = $this->performRequest($method, $uri, $data, $header, $client);
        $model->setClean();

        return $modelResponse;
    }

    /** @return string userAgentString */
    protected function getUserAgentString(): string
    {
        return implode(' ', $this->userAgent);
    }

    protected function performRequest(
        string $method,
        string $uri,
        array $data = [],
        array $headers = [],
        ?ClientInterface $oClient = null
    ): ModelResponse {
        $this->setHeader($headers);
        //VALIDATE HEADERS
        $hHeaders = $this->getHeaders();
        $sMethod = strtoupper($method);

        $mainUrl = $this->sandbox ? self::ENKAP_API_URL_SANDBOX : self::ENKAP_API_URL_LIVE;

        $endPoint = $mainUrl . $uri;

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

            if (!in_array($requestResponse->getStatusCode(), [200, 201])) {
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

            $data = $sMethod === self::DELETE_REQUEST ? [] : [$response->getJson()];

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

    protected function setHeader(array $option = []): void
    {
        $this->headers += $option;
    }

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

    protected function getRequest(
        Configuration $configuration,
        string $type,
        string $uri,
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
