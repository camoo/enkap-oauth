<?php
declare(strict_types=1);

namespace Enkap\OAuth\Model;

use Enkap\OAuth\Interfaces\ModelInterface;
use StdClass;

class Token implements ModelInterface
{
    private const MODEL_NAME = 'Token';
    private $token;

    public function __construct(StdClass $token)
    {
        $this->token = $token;
    }

    public function getExpiresIn(): int
    {
        return $this->token->expires_in;
    }

    public function getAccessToken(): string
    {
        return $this->token->access_token;
    }

    public function getTokenType(): ?string
    {
        return $this->token->token_type;
    }

    public function getScope(): string
    {
        return $this->token->scope;
    }

    public function getModelName(): string
    {
        return self::MODEL_NAME;
    }

}
