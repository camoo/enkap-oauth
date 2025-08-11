<?php

declare(strict_types=1);

namespace Enkap\OAuth\Http;

use Enkap\OAuth\Model\ModelCollection;

class ModelResponse
{
    private ModelCollection $collection;

    /** @var array|string[] $headers */
    private array $headers;

    private int $code;

    public function __construct(ModelCollection $collection, int $code, array $headers)
    {
        $this->collection = $collection;
        $this->headers = $headers;
        $this->code = $code;
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
