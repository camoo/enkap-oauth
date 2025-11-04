<?php

declare(strict_types=1);

namespace Enkap\OAuth\Query;

use Enkap\OAuth\Http\ModelResponse;
use Enkap\OAuth\Interfaces\ModelInterface;

class ModelQuery
{
    private array $whereData;

    public function __construct(private readonly ModelInterface $model)
    {
    }

    /** @param array<string, mixed> $where */
    public function where(array $where): self
    {
        $this->whereData = $where;

        return $this;
    }

    public function execute(): ModelResponse
    {
        return $this->model->getClient()->get($this->model, $this->whereData);
    }
}
