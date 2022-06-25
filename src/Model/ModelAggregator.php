<?php

declare(strict_types=1);

namespace Enkap\OAuth\Model;

use Enkap\OAuth\Exception\EnkapException;
use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Query\ModelQuery;

class ModelAggregator
{
    private $model;

    public function __construct(ModelInterface $model)
    {
        $this->model = $model;
    }

    public function has($offset): bool
    {
        return $this->model->__isset($offset);
    }

    public function get($offset)
    {
        return $this->model->__get($offset);
    }

    public function set($offset, $value)
    {
        return $this->model->__set($offset, $value);
    }

    /**
     * If the object supports a specific HTTP method.
     */
    public function isMethodSupported(string $method): bool
    {
        return in_array($method, $this->model::getSupportedMethods(), true);
    }

    public function find(): ModelQuery
    {
        if ($this->model->getClient() === null) {
            throw new EnkapException(
                '->get() is only available on objects that have an injected Http client context.'
            );
        }

        return new ModelQuery($this->model);
    }
}
