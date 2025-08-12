<?php

declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Camoo\Cache\Cache;
use Camoo\Cache\CacheConfig;
use Enkap\OAuth\Model\CallbackUrl;
use Throwable;

class CallbackUrlService extends BaseService
{
    public function set(CallbackUrl $callbackUrl): bool
    {
        try {
            $cryptoSalt = $_ENV['CRYPTO_SALT'] ?? null;
            $cacheEncrypt = null !== $cryptoSalt;
            $cache = new Cache(CacheConfig::fromArray(['crypto_salt' => $cryptoSalt, 'encrypt' => $cacheEncrypt]));
            $tokenCacheKeySuffix = $this->client->getSandbox() ? '_dev' : '_pro';
            $tokenCacheKey = md5('\\Enkap\\OAuth\\' . 'token') . $tokenCacheKeySuffix;
            $cache->delete($tokenCacheKey);
            $callbackUrl->setClient($this->client);
            $callbackUrl->save();
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
