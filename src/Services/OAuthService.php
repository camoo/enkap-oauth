<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Camoo\Cache\Cache;
use Camoo\Cache\CacheConfig;
use Enkap\OAuth\Exception\EnKapAccessTokenException;
use Enkap\OAuth\Http\Client;
use Enkap\OAuth\Http\ClientFactory;
use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Model\Token;
use GuzzleHttp\Exception\GuzzleException;
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
        $cryptoSalt = $_ENV['CRYPTO_SALT'] ?? null;
        $cacheEncrypt = null !== $cryptoSalt;
        $this->cache = new Cache(CacheConfig::fromArray(['crypto_salt' => $cryptoSalt, 'encrypt' => $cacheEncrypt]));
    }

    protected function getClient(): Client
    {
        return call_user_func([ClientFactory::class, 'create'], $this, 'Token');
    }

    public function getAccessToken(): string
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
            $accessToken = $response->getAccessToken();
            $expiresIn = $response->getExpiresIn();
            $this->cache->write('token', $accessToken, $expiresIn);
        }
        return $accessToken;
    }

    /**
     * @return ModelInterface|null|Token
     * @throws GuzzleException
     */
    protected function apiCall(): ?ModelInterface
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

        return $response->getResult()->firstOrFail();
    }
}
