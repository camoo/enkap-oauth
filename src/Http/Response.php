<?php

declare(strict_types=1);

namespace Enkap\OAuth\Http;

use Enkap\OAuth\Lib\Json;

/**
 * Class Response
 *
 * @author CamooSarl
 */
class Response
{
    protected Json $jsonData;

    private int $statusCode;

    private string $content;

    private array $headers;

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->jsonData = new Json($content);
        $this->headers = $headers;
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
        if (!in_array($this->getStatusCode(), [200, 201])) {
            $message = $this->content !== '' ? $this->content : 'request failed!';

            return ['message' => $message];
        }

        return $this->jsonData->decode();
    }
}
