<?php

declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Camoo\Cache\Cache;
use Camoo\Cache\CacheConfig;
use Enkap\OAuth\Enum\GrantType;
use Enkap\OAuth\Enum\HttpStatus;
use Enkap\OAuth\Exception\EnKapAccessTokenException;
use Enkap\OAuth\Http\Client;
use Enkap\OAuth\Http\ClientFactory;
use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Model\Token;
use Throwable;

final class OAuthService
{
    private const DEFAULT_GRANT_TYPE = 'client_credentials';

    private const DEFAULT_TYPE = 'Token';

    private Cache $cache;

    public function __construct(
        private readonly string $consumerKey,
        private readonly string $consumerSecret,
        private readonly bool $sandbox = false,
        private readonly bool $clientDebug = false
    ) {
        $cryptoSalt = $_ENV['CRYPTO_SALT'] ?? null;
        $this->cache = new Cache(CacheConfig::fromArray([
            'crypto_salt' => $cryptoSalt,
            'encrypt' => $cryptoSalt !== null,
        ]));
    }

    /**
     * Request an access token for the selected grant.
     *
     * @param array{
     *   scope?: string,
     *   // PASSWORD
     *   username?: string, password?: string,
     *   // REFRESH_TOKEN
     *   refresh_token?: string,
     *   // AUTH_CODE
     *   code?: string, redirect_uri?: string, code_verifier?: string,
     *   // JWT_BEARER
     *   assertion?: string,
     *   // SAML2_BEARER
     *   saml_assertion?: string,
     *   // TOKEN_EXCHANGE (RFC 8693)
     *   subject_token?: string, subject_token_type?: string,
     *   actor_token?: string, actor_token_type?: string,
     *   audience?: string, requested_token_type?: string
     * } $params
     */
    public function getAccessToken(?GrantType $grant = null, array $params = []): string
    {
        $grant ??= $this->getGrantType();
        // cache key includes env + grant + identity of the request
        $cacheSuffix = $this->sandbox ? '_dev' : '_pro';
        $cacheDiscriminator = match ($grant) {
            GrantType::PASSWORD => $params['username'] ?? '',
            GrantType::REFRESH_TOKEN => substr(hash('xxh3', (string)($params['refresh_token'] ?? '')), 0, 16),
            GrantType::AUTH_CODE => substr(hash('xxh3', (string)($params['code'] ?? '')), 0, 16),
            GrantType::TOKEN_EXCHANGE => substr(hash('xxh3', ($params['subject_token'] ?? '') . '|' . ($params['audience'] ?? '')), 0, 16),
            default => '',
        };

        $tokenCacheKey = md5("\\Enkap\\OAuth\\token|{$grant->value}|{$cacheDiscriminator}") . $cacheSuffix;
        if ($cached = $this->cache->read($tokenCacheKey)) {
            return $cached;
        }

        try {
            /** @var Token|null $response */
            $response = $this->apiCall($grant, $params);
        } catch (Throwable $exception) {
            throw new EnKapAccessTokenException($exception->getMessage(), (int)$exception->getCode(), $exception);
        }

        if ($response === null) {
            throw new EnKapAccessTokenException('Access Token cannot be retrieved. Please check your credentials');
        }

        $accessToken = $response->getAccessToken();
        $ttl = max(1, $response->getExpiresIn() - 60);
        $this->cache->write($tokenCacheKey, $accessToken, $ttl);

        return $accessToken;
    }

    private function getGrantType(): GrantType
    {
        $grantType = $_ENV['GRANT_TYPE'] ?? self::DEFAULT_GRANT_TYPE;
        try {
            return GrantType::fromName($grantType);
        } catch (Throwable $exception) {
            throw new EnKapAccessTokenException(sprintf('Invalid grant type: %s', $grantType), 0, $exception);
        }
    }

    private function getClient(): Client
    {
        $client = ClientFactory::create($this, self::DEFAULT_TYPE);
        $client->setSandbox($this->sandbox);
        $client->setDebug($this->clientDebug);

        return $client;
    }

    private function apiCall(GrantType $grant, array $params): ?ModelInterface
    {
        $header = [
            'Authorization' => 'Basic ' . base64_encode(sprintf('%s:%s', $this->consumerKey, $this->consumerSecret)),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $body = $this->buildBody($grant, $params);

        $response = $this->getClient()->post('/token?' . http_build_query($body), [], $header);

        if ($response->getStatusCode() !== HttpStatus::OK->value) {
            return null;
        }

        return $response->getResult()->firstOrFail();
    }

    /**
     * Build the request body based on the grant type and parameters.
     *
     * @param array<string,mixed> $params
     *
     * @return array<string,string>
     */
    private function buildBody(GrantType $grant, array $params): array
    {
        // Common base
        $body = ['grant_type' => $grant->value];

        // Optional shared param
        if (!empty($params['scope'])) {
            $body['scope'] = (string)$params['scope'];
        }

        // Grant-specific requirements
        return match ($grant) {
            GrantType::CLIENT_CREDENTIALS, GrantType::IWA_NTLM => $body,

            GrantType::PASSWORD => $this->requireParams($body, $params, ['username', 'password']),

            GrantType::REFRESH_TOKEN => $this->requireParams($body, $params, ['refresh_token']),

            GrantType::AUTH_CODE => $this->requireParams(
                $body + ['redirect_uri' => (string)($params['redirect_uri'] ?? '')],
                $params,
                ['code', 'redirect_uri'],
                optional: ['code_verifier']
            ),

            GrantType::JWT_BEARER => $this->requireParams($body, $params, ['assertion']),

            GrantType::SAML2_BEARER => $this->requireParams($body, $params, ['saml_assertion']),

            GrantType::TOKEN_EXCHANGE => $this->requireParams(
                $body,
                $params,
                ['subject_token', 'subject_token_type'],
                optional: ['actor_token', 'actor_token_type', 'audience', 'requested_token_type']
            ),
        };
    }

    /**
     * @param array<string,string> $base
     * @param array<string,mixed>  $params
     * @param string[]             $required
     * @param string[]             $optional
     *
     * @return array<string,string>
     */
    private function requireParams(array $base, array $params, array $required, array $optional = []): array
    {
        foreach ($required as $key) {
            if (!isset($params[$key]) || $params[$key] === '') {
                throw new EnKapAccessTokenException(sprintf('Missing required parameter: %s', $key));
            }
            $base[$key] = (string)$params[$key];
        }
        foreach ($optional as $key) {
            if (isset($params[$key]) && $params[$key] !== '') {
                $base[$key] = (string)$params[$key];
            }
        }

        return $base;
    }
}
