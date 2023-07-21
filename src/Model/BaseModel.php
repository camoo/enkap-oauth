<?php

declare(strict_types=1);

namespace Enkap\OAuth\Model;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Enkap\OAuth\Exception\EnkapException;
use Enkap\OAuth\Http\Client;
use Enkap\OAuth\Http\ModelResponse;
use Enkap\OAuth\Interfaces\ModelInterface;
use Enkap\OAuth\Lib\Helper;
use Enkap\OAuth\Query\ModelQuery;
use Exception;

/**
 * Class Model.
 *
 * @method bool       isMethodSupported(string $method)
 * @method bool       has($offset)
 * @method mixed      get($offset)
 * @method mixed      set($offset, $value)
 * @method ModelQuery find()
 */
abstract class BaseModel implements ModelInterface
{
    /** Keys for the meta properties array. */
    public const KEY_MANDATORY = 0;

    public const KEY_TYPE = 1;

    public const KEY_PHP_TYPE = 2;

    public const KEY_IS_ARRAY = 3;

    public const PROPERTY_TYPE_STRING = 'string';

    public const PROPERTY_TYPE_INT = 'int';

    public const PROPERTY_TYPE_FLOAT = 'float';

    public const PROPERTY_TYPE_BOOLEAN = 'bool';

    public const PROPERTY_TYPE_DATE = 'date';

    public const PROPERTY_TYPE_TIMESTAMP = 'timestamp';

    public const PROPERTY_TYPE_OBJECT = 'object';

    /**
     * Container to the actual properties of the object.
     *
     * @var array
     */
    protected $_data;

    /**
     * Holds a record of which properties have been changed.
     *
     * @var array
     */
    protected $_dirty;

    /**
     * Holds a list of objects that hold child references to this one.
     *
     * @var self[]
     */
    protected $_associated_objects;

    /**
     * Holds a ref to the application that was used to load the object,
     * enables shorthand $object->save();.
     */
    protected ?Client $client;

    public function __construct(?Client $client = null)
    {
        $this->_dirty = [];
        $this->_data = [];
        $this->_associated_objects = [];
        $this->client = $client;
    }

    public function __call(string $method, array $params): mixed
    {
        if (!method_exists(ModelAggregator::class, $method)) {
            throw new EnkapException(sprintf('Method %s not found in %s', $method, get_class($this)));
        }
        $aggregator = new ModelAggregator($this);

        return call_user_func_array([$aggregator, $method], $params);
    }

    /** Magic method for testing if properties exist. */
    public function __isset(string $property): bool
    {
        return isset($this->_data[$property]);
    }

    /** Magic getter for accessing properties directly. */
    public function __get(string $property): mixed
    {
        $getter = sprintf('get%s', Helper::camelize($property, true));

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        throw new EnkapException(sprintf("Undefined property %s::$%s.\n", __CLASS__, $property));
    }

    /** Magic setter for setting properties directly. */
    public function __set(string $property, mixed $value): void
    {
        $setter = sprintf('set%s', Helper::camelize($property));

        if (method_exists($this, $setter)) {
            $this->$setter($value);

            return;
        }

        throw new EnkapException(sprintf("Undefined property %s::$%s.\n", __CLASS__, $property));
    }

    public function setClient(Client $client): void
    {
        if (null !== $this->client) {
            return;
        }
        $this->client = $client;
    }

    /** If there have been any properties changed since load. */
    public function isDirty(?string $property = null): bool
    {
        if ($property === null) {
            return count($this->_dirty) > 0;
        }

        return isset($this->_dirty[$property]);
    }

    /** Manually set a property as dirty. */
    public function setDirty(string $property): BaseModel
    {
        $this->_dirty[$property] = true;

        return $this;
    }

    /** Manually set a property as clean. */
    public function setClean(?string $property = null): BaseModel
    {
        if ($property === null) {
            $this->_dirty = [];
        } else {
            unset($this->_dirty[$property]);
        }

        return $this;
    }

    /**
     * Load an assoc array into the instance of the object $property => $value
     * $replace_data - replace existing data.
     *
     * @throws Exception
     */
    public function fromStringArray($input_array, bool $replace_data = false)
    {
        foreach (static::getProperties() as $property => $meta) {
            $type = $meta[self::KEY_TYPE];
            $php_type = $meta[self::KEY_PHP_TYPE] ?? null;
            $isArray = $meta[self::KEY_IS_ARRAY];

            //If set and NOT replace data, continue
            if (!$replace_data && isset($this->_data[$property])) {
                continue;
            }

            if (!isset($input_array[$property])) {
                $this->_data[$property] = null;

                continue;
            }

            if ($isArray && !is_array($input_array[$property])) {
                $this->_data[$property] = null;

                continue;
            }

            if ($isArray && Helper::isAssoc($input_array[$property]) === false) {
                $collection = new Collection();
                $collection->addAssociatedObject($property, $this);
                foreach ($input_array[$property] as $assoc_element) {
                    $cast = self::castFromString($type, $assoc_element, $php_type);
                    //Do this here so that you know it's not a static method call to ::castFromString
                    if ($cast instanceof self) {
                        $cast->addAssociatedObject($property, $this);
                    }
                    $collection->append($cast);
                }
                $this->_data[$property] = $collection;
            } else {
                $cast = self::castFromString($type, $input_array[$property], $php_type);
                //Do this here so that you know it's not a static method call to ::castFromString
                if ($cast instanceof self) {
                    $cast->addAssociatedObject($property, $this);
                }
                $this->_data[$property] = $cast;
            }
        }
    }

    /**
     * Convert the object into an array, and any non-primitives to string.
     */
    public function toStringArray($dirty_only = false): array
    {
        $out = [];
        foreach (static::getProperties() as $property => $meta) {
            if (!isset($this->_data[$property])) {
                continue;
            }

            //if we only want the dirty props, stop here
            if ($dirty_only && !isset($this->_dirty[$property])) {
                continue;
            }

            $type = $meta[self::KEY_TYPE];

            if ($this->_data[$property] instanceof Collection) {
                $out[$property] = [];
                foreach ($this->_data[$property] as $assoc_property) {
                    $out[$property][] = self::castToString($type, $assoc_property);
                }
            } else {
                $out[$property] = self::castToString($type, $this->_data[$property]);
            }
        }

        return $out;
    }

    /**
     * Validate the object and (optionally) the child objects recursively.
     *
     * @throws Exception
     */
    public function validate(bool $check_children = true): bool
    {
        //validate
        foreach (static::getProperties() as $property => $meta) {
            $mandatory = $meta[self::KEY_MANDATORY];

            //If it's got a GUID, it's already going to be valid almost all cases
            if ($mandatory) {
                if (!isset($this->_data[$property]) || empty($this->_data[$property])) {
                    throw new EnkapException(
                        sprintf(
                            '%s::$%s is mandatory and is either missing or empty.',
                            get_class($this),
                            $property
                        )
                    );
                }

                if ($check_children) {
                    if ($this->_data[$property] instanceof self) {
                        //Keep IDEs happy
                        $obj = $this->_data[$property];
                        $obj->validate();
                    } elseif ($this->_data[$property] instanceof Collection) {
                        foreach ($this->_data[$property] as $element) {
                            if ($element instanceof self) {
                                $element->validate();
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /** Convert properties to strings, based on the types parsed. */
    public static function castToString(mixed $type, mixed $value): mixed
    {
        if ($value === '') {
            return '';
        }

        return match ($type) {
            self::PROPERTY_TYPE_BOOLEAN => $value ? 'true' : 'false',
            self::PROPERTY_TYPE_DATE => $value->format('Y-m-d'),
            self::PROPERTY_TYPE_TIMESTAMP => $value->format('c'),
            self::PROPERTY_TYPE_OBJECT => $value instanceof self ? $value->toStringArray() : '',
            default => is_scalar($value) ? (string)$value : ''
        };
    }

    /**
     * Cast the values to PHP types.
     *
     * @throws Exception
     *
     * @return bool|DateTimeInterface|float|int|string
     */
    public static function castFromString($type, mixed $value, ?string $phpType)
    {
        //Here should maybe handle locale specific tz overrides in the future.
        $timezone = null;

        switch ($type) {
            case self::PROPERTY_TYPE_INT:
                return (int)$value;
            case self::PROPERTY_TYPE_FLOAT:
                return (float)$value;
            case self::PROPERTY_TYPE_BOOLEAN:
                return in_array(strtolower($value), ['true', '1', 'yes'], true);
            case self::PROPERTY_TYPE_TIMESTAMP:
                $timezone = new DateTimeZone('UTC');

                // no break
            case self::PROPERTY_TYPE_DATE:
                if (preg_match('/Date\\((?<timestamp>[0-9+.]+)\\)/', $value, $matches)) {
                    $value = $matches['timestamp'];
                }

                return new DateTime($value, $timezone);
            case self::PROPERTY_TYPE_OBJECT:
                /** @var self $instance */
                $instance = new $phpType();
                $instance->fromStringArray($value);

                return $instance;
            default:
                if (is_scalar($value)) {
                    return (string)$value;
                }

                return (object)$value;
        }
    }

    public function save(): ModelResponse
    {
        if ($this->client === null) {
            throw new EnkapException(
                '->save() is only available on objects that have an injected Http client context.'
            );
        }

        return $this->client->save($this);
    }

    public function delete(): ModelResponse
    {
        if ($this->client === null) {
            throw new EnkapException(
                '->delete() is only available on objects that have an injected Http client context.'
            );
        }

        return $this->client->save($this, true);
    }

    public function addAssociatedObject(string $property, self $object): void
    {
        $this->_associated_objects[$property] = $object;
    }

    public function unset(string $offset): void
    {
        unset($this->_data[$offset]);
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    protected function propertyUpdated(string $property, mixed $value): void
    {
        if (!isset($this->_data[$property]) || $this->_data[$property] !== $value) {
            //If this object can update itself, set its own dirty flag, otherwise, set its parent's.
            if (count(array_intersect(
                static::getSupportedMethods(),
                [Client::PUT_REQUEST, Client::POST_REQUEST]
            )) > 0) {
                //Object can update itself
                $this->setDirty($property);
            } else {
                //Object can't update itself, so tell its parents
                foreach ($this->_associated_objects as $parent_property => $object) {
                    $object->setDirty($parent_property);
                }
            }
        }
    }
}
