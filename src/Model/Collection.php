<?php

namespace Enkap\OAuth\Model;

use ArrayObject;

class Collection extends ArrayObject
{
    /** @var BaseModel[] */
    protected array $associatedObjects;

    public function addAssociatedObject($parent_property, BaseModel $object): void
    {
        $this->associatedObjects[$parent_property] = $object;
    }

    /** Return whether the Collection is 0 */
    public function empty(): bool
    {
        return !count($this);
    }

    /**
     * Remove an item at a specific index.
     */
    public function removeAt($index): void
    {
        if (isset($this[$index])) {
            foreach ($this->associatedObjects as $parent_property => $object) {

                $object->setDirty($parent_property);
            }
            unset($this[$index]);
        }
    }

    /** Remove a specific object from the collection. */
    public function remove(BaseModel $object): void
    {
        foreach ($this as $index => $item) {
            if ($item === $object) {
                $this->removeAt($index);
            }
        }
    }

    /** Remove all the values' in the collection. */
    public function removeAll(): void
    {
        foreach ($this->associatedObjects as $parent_property => $object) {
            $object->setDirty($parent_property);
        }
        $this->exchangeArray([]);
    }

    public function first()
    {
        return $this->offsetExists(0) ? $this->offsetGet(0) : null;
    }

    public function last()
    {
        $last = $this->count() - 1;

        return $this->offsetExists($last) ? $this->offsetGet($last) : null;
    }
}
