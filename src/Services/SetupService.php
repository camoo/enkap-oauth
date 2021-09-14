<?php
declare(strict_types=1);

namespace Enkap\OAuth\Services;

use Enkap\OAuth\Model\CallbackUrl;
use Throwable;

class SetupService extends BaseService
{

    public function setCallbackUrls(CallbackUrl $callbackUrl): bool
    {
        try {
            $callbackUrl->save($this->authService);
        } catch (Throwable $e) {
            return false;
        }
        return true;
    }
}
