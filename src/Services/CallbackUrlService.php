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
            $callbackUrl->save();
        } catch (Throwable $e) {
            return false;
        }
        return true;
    }
}
