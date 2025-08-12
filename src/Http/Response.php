<?php

declare(strict_types=1);

namespace Enkap\OAuth\Http;

use Enkap\OAuth\Enum\HttpStatus;
use Enkap\OAuth\Lib\Json;

/**
 * Class Response
 *
 * @author CamooSarl
 */
class Response
{
    private Json $jsonInstance;

    /**
     * @param string[] $headers
     */
    public function __construct(
        private readonly string $content = '',
        private readonly int $statusCode = HttpStatus::OK->value,
        private readonly array $headers = []
    ) {
        $this->jsonInstance = new Json($this->content);
    }

    public function getBody(): string
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getJson(): array
    {
        if (!in_array($this->getStatusCode(), [HttpStatus::OK->value, HttpStatus::CREATED->value])) {
            $message = $this->content !== '' ? $this->content : 'request failed!';

            return ['message' => $message];
        }

        return $this->jsonInstance->decode();
    }
}
