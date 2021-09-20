<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Model\CallbackUrl;
use Throwable;

class CallbackUrlService extends BaseService
{

    public function set(CallbackUrl $callbackUrl): bool
    {
        try {
            $callbackUrl->setClient($this->client);
            $callbackUrl->save();
        } catch (Throwable $exception) {
            return false;
        }
        return true;
    }
}
