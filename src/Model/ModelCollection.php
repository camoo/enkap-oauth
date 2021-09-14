<?php
declare(strict_types=1);

namespace Enkap\OAuth\Model;

use ArrayIterator;
use Enkap\OAuth\Exception\EnkapModelNotFoundException;
use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Lib\Json;
use IteratorAggregate;

class ModelCollection implements IteratorAggregate
{
    /** @var ModelInterface[]|string[] $values */
    private $values = [];

    private function __construct(array $items, ?string $returnType)
    {
        foreach ($items as $value) {
            $this->add($value, $returnType);
        }
    }

    public static function create(array $items, ?string $returnType): ModelCollection
    {
        return new self($items, $returnType);
    }

    public function add(array $item, ?string $returnType = null): void
    {
        if (empty($returnType)) {
            $this->values[] = $item;
            return;
        }

        if (!empty($item->message)) {
            $json = new Json($item->message);
            $item = $json->decode();
        }

        $class = __NAMESPACE__ . '\\' . $returnType;
        $model = new $class();
        $model->fromStringArray($item);
        $this->values[] = $model;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }

    public function first(): ?ModelInterface
    {
        if (empty($this->values)) {
            return null;
        }
        return $this->values[0];
    }

    public function get(int $position)
    {
        if (!array_key_exists($position, $this->values)) {
            return null;
        }
        return $this->values[$position];
    }

    /**
     * @return ModelInterface
     */
    public function firstOrFail(): ModelInterface
    {
        if (empty($this->values)) {
            throw new EnkapModelNotFoundException('Entity at position "0" Not found');
        }
        return $this->values[0];
    }

    public function getOrFail(int $position): ModelInterface
    {
        if (!array_key_exists($position, $this->values)) {
            throw new EnkapModelNotFoundException(sprintf('Entity at position "%d" Not found', $position));
        }
        return $this->values[$position];
    }

    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    public function count(): int
    {
        return count($this->values);
    }

    public function toArray(): array
    {
        return $this->values;
    }
}
