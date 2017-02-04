<?php

namespace Tooleks\Laravel\Presenter;

use Illuminate\Contracts\{
    Support\Arrayable,
    Support\Jsonable
};
use JsonSerializable;
use Tooleks\Laravel\Presenter\{
    Contracts\InvalidArgumentException as InvalidArgumentExceptionContract,
    Contracts\PresenterException as PresenterExceptionContract,
    Exceptions\InvalidArgumentException,
    Exceptions\PresenterException
};

/**
 * Class Presenter.
 *
 * @property object|array $presentee
 * @package Tooleks\Laravel\Presenter
 * @author Oleksandr Tolochko <tooleks@gmail.com>
 */
abstract class Presenter implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * Presentee model.
     *
     * @var object|array
     */
    protected $presentee;

    /**
     * Presenter constructor.
     *
     * @param object|array $presentee
     */
    public function __construct($presentee)
    {
        $this->assertPresentee($presentee);

        $this->presentee = $presentee;
    }

    /**
     * Assert presentee model.
     *
     * @param object|array $presentee
     * @throws InvalidArgumentExceptionContract
     */
    protected function assertPresentee($presentee)
    {
        if (!is_array($presentee) && !is_object($presentee)) {
            throw new InvalidArgumentException('Presentee should be an object or an array.');
        }
    }

    /**
     * Get presentee.
     *
     * @return object|array
     */
    public function getPresentee()
    {
        return $this->presentee;
    }

    /**
     * Get the array map of presenter attributes mapped to presentee attributes.
     *
     * Override this method to build attributes map.
     *
     * Example: return [
     *     ...
     *     'presenter_attribute_name' => 'presentee_attribute_name',
     *     ...
     * ];
     *
     * @return array
     */
    abstract protected function getAttributesMap() : array;

    /**
     * Magical attributes setter.
     *
     * @param string $attributeName
     * @param mixed $attributeValue
     * @throws PresenterExceptionContract
     */
    public function __set($attributeName, $attributeValue)
    {
        throw new PresenterException('Attribute modification is not allowed.');
    }

    /**
     * Magical attributes getter for mapping presenter attributes to presentee attributes.
     *
     * @param string $attributeName
     * @return mixed|null
     */
    public function __get($attributeName)
    {
        return $this->hasAttributeInMap($attributeName) ? $this->getAttributeViaMap($attributeName) : null;
    }

    /**
     * Determine if an attribute exists in the attributes map.
     *
     * @param string $attributeName
     * @return bool
     */
    protected function hasAttributeInMap(string $attributeName) : bool
    {
        return array_key_exists($attributeName, $this->getAttributesMap());
    }

    /**
     * Get the value of an attribute using the attributes map.
     *
     * @param string $attributeName
     * @return mixed|null
     */
    protected function getAttributeViaMap(string $attributeName)
    {
        $presenteeAttribute = $this->getAttributesMap()[$attributeName] ?? null;

        if (is_callable($presenteeAttribute)) {
            return $this->processCallback($presenteeAttribute);
        }

        if (is_string($attributeName) || is_numeric($attributeName)) {
            return $this->getPresenteeAttribute($presenteeAttribute);
        }

        return null;
    }

    /**
     * Process callback function.
     *
     * @param callable $callback
     * @return mixed
     */
    protected function processCallback(callable $callback)
    {
        return call_user_func($callback, $this->presentee);
    }

    /**
     * Get presentee attribute.
     *
     * @param string $attributeName
     * @return mixed|null
     */
    protected function getPresenteeAttribute(string $attributeName)
    {
        if (is_null($attributeName) || trim($attributeName) === '') {
            return null;
        }

        $attribute = $this->presentee;

        foreach (explode('.', $attributeName) as $segment) {
            if (is_array($attribute) && isset($attribute[$segment])) {
                $attribute = $attribute[$segment];
            } elseif (is_object($attribute) && isset($attribute->{$segment})) {
                $attribute = $attribute->{$segment};
            } else {
                $attribute = null;
                break;
            }
        }

        return $attribute;
    }

    /**
     * Convert the object to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        $array = [];

        $attributes = array_keys($this->getAttributesMap());
        foreach ($attributes as $attribute) {
            $array[$attribute] = $this->{$attribute};
        }

        return $array;
    }

    /**
     * @inheritdoc
     */
    public function toJson($options = JSON_ERROR_NONE)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
