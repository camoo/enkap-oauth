<?php
declare(strict_types=1);

namespace Enkap\OAuth\Http;

use Enkap\OAuth\Model\ModelCollection;

class ModelResponse
{

    /**
     * @var ModelCollection
     */
    private $collection;

    /** @var array $headers */
    private $headers;
    /**
     * @var int
     */
    private $code;

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

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getResult(): ModelCollection
    {
        return $this->collection;
    }

}
