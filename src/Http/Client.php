<?php
declare(strict_types=1);

namespace Enkap\OAuth\Http;

use Enkap\OAuth\Exception\EnkapBadResponseException;
use Enkap\OAuth\Exception\EnkapException;
use Enkap\OAuth\Exception\EnkapHttpClientException;
use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Lib\Helper;
use Enkap\OAuth\Services\OAuthService;
use Enkap\OAuth\Model\ModelCollection;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
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
    /** @var string|null $returnType */
    private $returnType;
    private const USER_AGENT_STRING = 'Enkap/CamooClient/%s (+https://github.com/camoo/enkap-oauth)';
    /** @var bool $sandbox */
    public $sandbox = false;

    /**
     * Debug switch (default set to false)
     *
     * @var bool
     */
    public $debug = false;

    /**
     * Debug file location (log to STDOUT by default)
     *
     * @var string
     */
    public $debugFile = 'php://output';

    /**
     * @var array
     */
    protected $userAgent = [];

    /**
     * @var array
     */
    protected $hRequestVerbs = [
        self::GET_REQUEST => RequestOptions::QUERY,
        self::POST_REQUEST => RequestOptions::FORM_PARAMS,
        self::PUT_REQUEST => null,
        self::DELETE_REQUEST => null,
    ];

    /**
     * @var array
     */
    private $_headers = [];
    /**
     * @var OAuthService
     */
    private $authService;

    /** @var array|string[] $clientOptions */
    private $clientOptions;

    /**
     * @param OAuthService $authService
     * @param string|null $returnType
     * @param array $clientOptions
     */
    public function __construct(
        OAuthService $authService,
        array        $clientOptions = [],
        ?string      $returnType = null
    )
    {
        $this->addUserAgentString($this->getAPIInfo());
        $this->addUserAgentString(Helper::getPhpVersion());
        $this->returnType = $returnType;
        $this->authService = $authService;
        $this->clientOptions = $clientOptions;
    }

    /**
     * Validate request params
     *
     * @param Validator $oValidator
     *
     * @return boolean
     */
    private function validatorDefault(Validator $oValidator): bool
    {
        $oValidator->rule('required', ['Authorization']);
        $oValidator->rule('optional', ['User-Agent']);
        return $oValidator->rule('in', 'request', array_keys($this->hRequestVerbs))->validate();
    }

    /**
     * @param string $userAgent
     */
    public function addUserAgentString(string $userAgent): void
    {
        $this->userAgent[] = $userAgent;
    }

    /**
     * @return string userAgentString
     */
    protected function getUserAgentString(): string
    {
        return implode(' ', $this->userAgent);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @param null $oClient
     *
     *
     * @return ModelResponse
     * @throws EnkapHttpClientException
     */
    protected function performRequest(
        string $method,
        string $uri,
        array  $data = [],
        array  $headers = [],
               $oClient = null
    ): ModelResponse
    {
        $this->setHeader($headers);
        //VALIDATE HEADERS
        $hHeaders = $this->getHeaders();
        $sMethod = strtoupper($method);

        $mainUrl = $this->sandbox ? self::ENKAP_API_URL_SANDBOX : self::ENKAP_API_URL_LIVE;

        $endPoint = $mainUrl . $uri;

        $oValidator = new Validator(array_merge(['request' => $sMethod], $hHeaders));

        $validateRequest = $this->validatorDefault($oValidator);

        if ($validateRequest === false) {
            throw new EnkapHttpClientException(json_encode($oValidator->errors()));
        }

        $defaultOption = [
            RequestOptions::TIMEOUT => self::ENKAP_CLIENT_TIMEOUT,
            RequestOptions::HEADERS => $hHeaders,
            RequestOptions::VERIFY => !$this->sandbox,
        ];

        if ($this->getDebug()) {
            $defaultOption[RequestOptions::DEBUG] = fopen($this->debugFile, 'a');
            if (!$defaultOption[RequestOptions::DEBUG]) {
                throw new EnkapHttpClientException('Failed to open the debug file: ' . $this->debugFile);
            }
        }
        try {
            $client = null === $oClient ? new GuzzleClient() : $oClient;

            if ($this->returnType === 'Token' || $sMethod === self::GET_REQUEST) {
                $defaultOption[$this->hRequestVerbs[$sMethod]] = $data;
                $data = [];
            }

            $this->clientOptions += $defaultOption;

            $request = $this->getRequest($sMethod, $endPoint, $data, $hHeaders);
            $oResponse = $client->send($request, $this->clientOptions);

            if (!in_array($oResponse->getStatusCode(), [200, 201])) {
                throw new EnkapBadResponseException(
                    (string)$oResponse->getBody(),
                    $oResponse->getStatusCode()
                );
            }

            $response = new Response(
                (string)$oResponse->getBody(),
                $oResponse->getStatusCode(),
                $oResponse->getHeaders()
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
        $this->_headers += $option;
    }

    protected function getHeaders(): array
    {
        $default = [
            'User-Agent' => $this->getUserAgentString()
        ];

        return $this->_headers += $default;
    }

    protected function getAPIInfo(): string
    {
        return sprintf(static::USER_AGENT_STRING, Helper::getPackageVersion());
    }

    public function post(string $uri, array $data = [], array $headers = [], $client = null): ModelResponse
    {
        return $this->performRequest(self::POST_REQUEST, $uri, $data, $headers, $client);
    }

    public function get(
        ModelInterface $model,
        array          $data = [],
        ?string        $uri = null,
        array          $headers = [],
                       $client = null
    ): ModelResponse
    {
        $this->returnType = $this->returnType ?? $model->getModelName();
        $suffix = $uri ?? $model->getResourceURI();
        if (!$this->sandbox) {
            $suffix = '/v1.2' . $suffix;
        }
        $uri = sprintf('/purchase%s', $suffix);
        $header = [
            'Authorization' => sprintf('Bearer %s', $this->authService->getAccessToken()),
        ];
        $headers += $header;
        return $this->performRequest(self::GET_REQUEST, $uri, $data, $headers, $client);
    }

    public function save(ModelInterface $model, bool $delete = false, $client = null): ModelResponse
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
        if (!$this->sandbox) {
            $suffix = '/v1.2' . $suffix;
        }
        $uri = sprintf('/purchase%s', $suffix);

        if (!$model->isMethodSupported($method)) {
            throw new EnkapException(sprintf('%s does not support [%s] via the API', get_class($model), $method));
        }

        $data = $model->toStringArray();

        $modelResponse = $this->performRequest(
            $method,
            $uri,
            $data,
            $header,
            $client
        );
        $model->setClean();
        return $modelResponse;
    }

    protected function getRequest(string $type, string $uri, array $data = [], array $headers = []): Request
    {
        $httpBody = json_encode($data);
        return new Request(
            $type,
            $uri,
            $headers,
            $httpBody
        );
    }

    private function getDebug(): bool
    {
        return $this->debug;
    }
}
