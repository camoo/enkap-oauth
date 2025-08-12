<?php

declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Camoo\Cache\Cache;
use Camoo\Cache\CacheConfig;
use Enkap\OAuth\Enum\HttpStatus;
use Enkap\OAuth\Exception\EnKapAccessTokenException;
use Enkap\OAuth\Http\Client;
use Enkap\OAuth\Http\ClientFactory;
use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Model\Token;
use Throwable;

class OAuthService
{
    private Cache $cache;

    public function __construct(
        private readonly string $consumerKey,
        private readonly string $consumerSecret,
        private readonly bool $sandbox = false,
        private readonly bool $clientDebug = false
    ) {
        $cryptoSalt = $_ENV['CRYPTO_SALT'] ?? null;
        $cacheEncrypt = null !== $cryptoSalt;
        $this->cache = new Cache(CacheConfig::fromArray(['crypto_salt' => $cryptoSalt, 'encrypt' => $cacheEncrypt]));
    }

    public function getAccessToken(): string
    {
        $tokenCacheKeySuffix = $this->sandbox ? '_dev' : '_pro';
        $tokenCacheKey = md5('\\Enkap\\OAuth\\' . 'token') . $tokenCacheKeySuffix;
        $accessToken = $this->cache->read($tokenCacheKey);
        if (!empty($accessToken)) {
            return $accessToken;
        }

        try {
            /** @var Token $response */
            $response = $this->apiCall();
        } catch (Throwable $exception) {
            throw new EnKapAccessTokenException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }

        if ($response === null) {
            throw new EnKapAccessTokenException(
                'Access Token cannot be retrieved. Please check your credentials'
            );
        }
        $accessToken = $response->getAccessToken();
        $expiresIn = $response->getExpiresIn();
        $this->cache->write($tokenCacheKey, $accessToken, $expiresIn);

        return $accessToken;
    }

    private function getClient(): Client
    {
        return ClientFactory::create($this, 'Token');
    }

    private function apiCall(): ?ModelInterface
    {
        $header = [
            'Authorization' => 'Basic ' . base64_encode(
                sprintf('%s:%s', $this->consumerKey, $this->consumerSecret)
            ),
        ];
        $client = $this->getClient();
        $client->setSandbox($this->sandbox);
        $client->setDebug($this->clientDebug);
        $response = $client->post('/token?grant_type=client_credentials', [], $header);

        if ($response->getStatusCode() !== HttpStatus::OK->value) {
            return null;
        }

        return $response->getResult()->firstOrFail();
    }
}
