<?php

namespace Enkap\OAuth\Model;

use ArrayObject;

class Collection extends ArrayObject
{
    /**
     * @var BaseModel[]
     */
    protected $_associated_objects;

    public function addAssociatedObject($parent_property, BaseModel $object)
    {
        $this->_associated_objects[$parent_property] = $object;
    }

    /**
     * Return whether the Collection is 0
     *
     * @return bool
     */
    public function empty(): bool
    {
        return !count($this);
    }

    /**
     * Remove an item at a specific index.
     *
     * @param $index
     */
    public function removeAt($index)
    {
        if (isset($this[$index])) {
            foreach ($this->_associated_objects as $parent_property => $object) {
                /**
                 * @var BaseModel
                 */
                $object->setDirty($parent_property);
            }
            unset($this[$index]);
        }
    }

    /**
     * Remove a specific object from the collection.
     *
     * @param BaseModel $object
     */
    public function remove(BaseModel $object)
    {
        foreach ($this as $index => $item) {
            if ($item === $object) {
                $this->removeAt($index);
            }
        }
    }

    /**
     *  Remove all the values' in the collection.
     */
    public function removeAll()
    {
        foreach ($this->_associated_objects as $parent_property => $object) {
            /**
             * @var BaseModel
             */
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
