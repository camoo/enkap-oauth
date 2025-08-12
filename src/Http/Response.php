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
readonly class Response
{
    public function __construct(
        private string $content = '',
        private int $statusCode = HttpStatus::OK->value,
        private array $headers = [],
        private ?Json $jsonData = null
    ) {
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

        return $this->getJsonInstance()->decode();
    }

    private function getJsonInstance(): Json
    {
        return $this->jsonData ?? new Json($this->content);
    }
}
