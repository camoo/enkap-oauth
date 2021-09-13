<?php
declare(strict_types=1);

namespace Enkap\OAuth\Lib;

use Camoo\Cache\Cache;
use Camoo\Cache\CacheConfig;
use Enkap\OAuth\Exception\EnKapAccessTokenException;
use Enkap\OAuth\Http\Client;
use Enkap\OAuth\Http\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;
use Throwable;


class OAuthService
{

    /**
     * @var string
     */
    private $consumerKey;
    /**
     * @var string
     */
    private $consumerSecret;
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(string $consumerKey, string $consumerSecret)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->cache = new Cache(CacheConfig::fromArray(['crypto_salt' => $_ENV['CRYPTO_SALT']]));
    }

    protected function getClient(): Client
    {
        return call_user_func([ClientFactory::class, 'create']);
    }

    public function getAccessToken() : string
    {
        $accessToken = $this->cache->read('token');
        if ($accessToken === false) {
            try {
                $response = $this->apiCall();
            } catch (Throwable $exception) {
                throw new EnKapAccessTokenException(
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception->getPrevious()
                );
            }

            if ($response === null) {
                throw new EnKapAccessTokenException(
                    'Access Token cannot be retrieved. Please check your credentials'
                );
            }
            $accessToken = $response->access_token;
            $expiresIn = $response->expires_in;
            $this->cache->write('token', $accessToken, $expiresIn);
        }
        return $accessToken;
    }


    /**
     * @throws GuzzleException
     */
    protected function apiCall(): ?StdClass
    {
        $header = [
            'Authorization' => 'Basic ' . base64_encode(
                sprintf('%s:%s', $this->consumerKey, $this->consumerSecret)
                )
        ];
        $response = $this->getClient()->post('/token', ['grant_type' => 'client_credentials',], $header);
        if ($response->getStatusCode() !== 200) {
            return null;
        }

        return $response->getJson();
    }
}
