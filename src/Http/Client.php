<?php
declare(strict_types=1);

namespace Enkap\OAuth\Http;

use Enkap\OAuth\Exception\EnkapHttpClientException;
use Enkap\OAuth\Lib\Helper;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client as GuzzleClient;
use Valitron\Validator;

use const ENKAP_CLIENT_VERSION;

/**
 * Class Client
 */
class Client
{
    private const GET_REQUEST = 'GET';
    private const POST_REQUEST = 'POST';
    private const ENKAP_API_URL = 'https://api.enkap.cm';
    private const ENKAP_CLIENT_TIMEOUT = 30;

    /**
     * @var array
     */
    protected $userAgent = [];

    /**
     * @var array
     */
    protected $hRequestVerbs = [self::GET_REQUEST => 'query', self::POST_REQUEST => 'form_params'];

    /**
     * @var int
     */
    private $timeout = self::ENKAP_CLIENT_TIMEOUT;

    /**
     * @var array
     */
    private $_headers = [];

    /**
     * @param int|null $timeout > 0
     */
    public function __construct(?int $timeout = null)
    {
        $this->addUserAgentString($this->getAPIInfo());
        $this->addUserAgentString(Helper::getPhpVersion());

        if (!is_int($timeout) || $timeout < 0) {
            throw new EnkapHttpClientException(sprintf(
                'Connection timeout must be an int >= 0, got "%s".',
                is_object($timeout) ? get_class($timeout) : gettype($timeout) . ' ' .
                    var_export($timeout, true)
            ));
        }
        if (!empty($timeout)) {
            $this->timeout = $timeout;
        }
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
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param null $oClient
     *
     *
     * @return Response
     * @throws GuzzleException
     */
    protected function performRequest(
        string $method,
        string $url,
        array  $data = [],
        array  $headers = [],
               $oClient = null
    ): Response
    {
        $this->setHeader($headers);
        //VALIDATE HEADERS
        $hHeaders = $this->getHeaders();
        $sMethod = strtoupper($method);
        $endPoint = self::ENKAP_API_URL . $url;

        $oValidator = new Validator(array_merge(['request' => $sMethod], $hHeaders));

        $validateRequest = $this->validatorDefault($oValidator);
        if ($validateRequest === false) {
            var_dump($oValidator->errors());

            throw new EnkapHttpClientException(json_encode($oValidator->errors()));
        }


        try {
            $client = null === $oClient ? new GuzzleClient(['timeout' => $this->timeout]) : $oClient;
            $oResponse = $client->request($sMethod, $endPoint,
                [$this->hRequestVerbs[$sMethod] => $data,
                    'headers' => $hHeaders
                ]
            );

            return new Response((string)$oResponse->getBody(), $oResponse->getStatusCode(), $oResponse->getHeaders());

        } catch (RequestException $exception) {
            throw new EnkapHttpClientException($exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious());
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
        $sIdentity = 'Enkap/ApiClient/';
        if (defined('WP_ENKAP_VERSION')) {
            $sWPV = '';
            global $wp_version;
            if ($wp_version) {
                $sWPV = $wp_version;
            }
            $sIdentity = 'WP' . $sWPV . '/SmobilPay' . WP_ENKAP_VERSION . ENKAP_DS;
        }
        return $sIdentity . ENKAP_CLIENT_VERSION;
    }


    /**
     * @throws GuzzleException
     */
    public function post(string $url, array $data = [], array $headers = []): Response
    {
        return $this->performRequest(self::POST_REQUEST, $url, $data, $headers);
    }

    /**
     * @throws GuzzleException
     */
    public function get(string $url, array $data = [], array $headers = []): Response
    {
        return $this->performRequest(self::GET_REQUEST, $url, $data, $headers);
    }

}
