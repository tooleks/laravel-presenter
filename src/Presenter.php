<?php

namespace Tooleks\Laravel\Presenter;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Tooleks\Laravel\Presenter\Exceptions\AttributeNotFoundException;
use Tooleks\Laravel\Presenter\Exceptions\InvalidArgumentException;
use Tooleks\Laravel\Presenter\Exceptions\PresenterException;

/**
 * Class Presenter.
 *
 * @property object|array wrappedModel
 * @package Tooleks\Laravel\Presenter
 * @author Oleksandr Tolochko <tooleks@gmail.com>
 */
abstract class Presenter implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * The wrapped model.
     *
     * @var object|array
     */
    protected $wrappedModel = null;

    /**
     * Assert the wrapped model.
     *
     * @throws PresenterException
     */
    protected function assertWrappedModel()
    {
        if (is_null($this->wrappedModel)) {
            throw new PresenterException('The wrapped model is not provided.');
        }
    }

    /**
     * Set the wrapped model.
     *
     * @param array|object $wrappedModel
     * @return $this
     */
    public function setWrappedModel($wrappedModel)
    {
        if (!is_array($wrappedModel) && !is_object($wrappedModel)) {
            throw new InvalidArgumentException('The wrapped model should be an object or an array.');
        }

        $this->wrappedModel = $wrappedModel;

        return $this;
    }

    /**
     * Get the wrapped model.
     *
     * @return object|array
     */
    public function getWrappedModel()
    {
        $this->assertWrappedModel();

        return $this->wrappedModel;
    }

    /**
     * Get the array map of the presenter attributes mapped to the wrapped model attributes.
     *
     * Override this method to build attributes map.
     *
     * Example: return [
     *     ...
     *     'presenter_attribute_name' => 'wrapped_model_attribute_name',
     *     ...
     * ];
     *
     * @return array
     */
    abstract protected function getAttributesMap(): array;

    /**
     * Attributes setter.
     *
     * @param string $attribute
     * @param mixed $value
     * @throws PresenterException
     */
    public function __set($attribute, $value)
    {
        throw new PresenterException('Attribute modification is not allowed.');
    }

    /**
     * Attributes getter for mapping the presenter attributes to the wrapped model attributes.
     *
     * @param string $attribute
     * @return mixed|null
     * @throws PresenterException
     */
    public function __get($attribute)
    {
        $this->assertWrappedModel();

        $wrappedModelAttribute = $this->getAttributesMap()[$attribute] ?? null;

        if (is_null($wrappedModelAttribute)) {
            throw new AttributeNotFoundException(sprintf('The presenter attribute "%s" not found.', $attribute));
        }

        if (is_callable($wrappedModelAttribute)) {
            return $wrappedModelAttribute($this->wrappedModel);
        }

        if (is_string($attribute) || is_float($attribute) || is_int($attribute) || is_bool($attribute)) {
            return $this->getWrappedModelAttribute($wrappedModelAttribute);
        }

        return null;
    }

    /**
     * Get the wrapped model attribute.
     *
     * @param string $attribute
     * @return mixed|null
     */
    public function getWrappedModelAttribute(string $attribute)
    {
        $this->assertWrappedModel();

        $value = $this->wrappedModel;

        // Loop for retrieving nested attributes declared by using "dot notation".
        foreach (explode('.', $attribute) as $nestedAttribute) {
            if (is_array($value) && isset($value[$nestedAttribute])) {
                $value = $value[$nestedAttribute];
            } elseif (is_object($value) && isset($value->{$nestedAttribute})) {
                $value = $value->{$nestedAttribute};
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
        $attributes = array_keys($this->getAttributesMap());

        foreach ($attributes as $attribute) {
            $array[$attribute] = $this->{$attribute};
        }

        return $array ?? [];
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
