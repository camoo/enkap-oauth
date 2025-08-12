<?php

declare(strict_types=1);

namespace Enkap\OAuth\Http;

use Enkap\OAuth\Model\ModelCollection;

readonly class ModelResponse
{
    /** @param  array|string[] $headers */
    public function __construct(private ModelCollection $collection, private int $code, private array $headers)
    {
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }

    /** @return array|string[] */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getResult(): ModelCollection
    {
        return $this->collection;
    }
}
