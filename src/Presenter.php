<?php

namespace Tooleks\Laravel\Presenter;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;
use JsonSerializable;
use InvalidArgumentException;
use LogicException;

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
     * Presentee.
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
     * Get presentee.
     *
     * @return object|array
     */
    public function getPresentee()
    {
        return $this->presentee;
    }

    /**
     * Assert presentee.
     *
     * @param object|array $presentee
     * @throws InvalidArgumentException
     */
    protected function assertPresentee($presentee)
    {
        if (!is_array($presentee) && !is_object($presentee)) {
            throw new InvalidArgumentException('Presentee should be an object or an array.');
        }
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
     * @throws LogicException
     */
    public function __set($attributeName, $attributeValue)
    {
        throw new LogicException('Attribute modification is not allowed.');
    }

    /**
     * Magical attributes getter for mapping presenter attributes to presentee attributes.
     *
     * @param string $attributeName
     * @return mixed|null
     */
    public function __get($attributeName)
    {
        if ($this->hasAttributeAccessor($attributeName)) {
            return $this->getAttributeViaAccessor($attributeName);
        }

        if ($this->hasAttributeInMap($attributeName)) {
            return $this->getAttributeViaMap($attributeName);
        }

        return null;
    }

    /**
     * Determine if an accessor method exists for an attribute.
     *
     * @param string $attributeName
     * @return bool
     * @internal param string $key
     */
    protected function hasAttributeAccessor(string $attributeName) : bool
    {
        return method_exists($this, 'get' . Str::studly($attributeName) . 'Attribute');
    }

    /**
     * Get the value of an attribute using its accessor method.
     *
     * @param string $attributeName
     * @return mixed
     */
    protected function getAttributeViaAccessor(string $attributeName)
    {
        return $this->{'get' . Str::studly($attributeName) . 'Attribute'}();
    }

    /**
     * Determine if an attribute exists in the attributes map.
     *
     * @param string $attributeName
     * @return bool
     */
    protected function hasAttributeInMap(string $attributeName) : bool
    {
        return key_exists($attributeName, $this->getAttributesMap());
    }

    /**
     * Get the value of an attribute using the attributes map.
     *
     * @param string $attributeName
     * @return mixed|null
     */
    protected function getAttributeViaMap(string $attributeName)
    {
        $presenteeAttributeName = $this->getAttributesMap()[$attributeName] ?? null;

        return $this->getPresenteeAttribute($presenteeAttributeName);
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

        $value = $this->presentee;

        foreach (explode('.', $attributeName) as $segment) {
            if (is_array($value) && isset($value[$segment])) {
                $value = $value[$segment];
            } elseif (is_object($value) && isset($value->{$segment})) {
                $value = $value->{$segment};
            } else {
                $value = null;
                break;
            }
        }

        return $value;
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
