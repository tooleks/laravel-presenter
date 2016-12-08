<?php

namespace Tooleks\Laravel\Presenter;

use Exception;
use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;

/**
 * Class ModelPresenter
 * @package Tooleks\Laravel\Presenter
 * @author Oleksandr Tolochko <tooleks@gmail.com>
 */
abstract class ModelPresenter implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * Original model object.
     *
     * @var mixed
     */
    protected $originalModel;

    /**
     * ModelPresenter constructor.
     *
     * @param mixed $originalModel
     */
    public function __construct($originalModel)
    {
        $this->originalModel = $originalModel;

        $this->validateOriginalModelClassType();
    }

    /**
     * Get original model class.
     *
     * Override this method to configure original model class.
     *
     * @return string
     */
    abstract protected function getOriginalModelClass() : string;

    /**
     * Get the array map of presenter attributes mapped to model attributes.
     *
     * Override this method to build attributes map.
     *
     * Example: return [
     *     ...
     *     'model_presenter_attribute_name' => 'original_model_attribute_name',
     *     ...
     * ];
     *
     * @return array
     */
    abstract protected function getAttributesMap() : array;

    /**
     * Validate original model class type.
     *
     * @return void
     * @throws Exception
     */
    protected function validateOriginalModelClassType()
    {
        $originalModelClass = $this->getOriginalModelClass();

        if (!class_exists($originalModelClass)) {
            throw new Exception(sprintf('"%s" class does not exist.', $originalModelClass));
        }

        if (!($this->originalModel instanceof $originalModelClass)) {
            throw new Exception(sprintf('The original model should be a "%s" class type.', $originalModelClass));
        }
    }

    /**
     * Get original model object.
     *
     * @return mixed
     */
    public function getOriginalModel()
    {
        return $this->originalModel;
    }

    /**
     * Magical attributes setter.
     *
     * @param string $attributeName
     * @param string $attributeValue
     * @throws Exception
     */
    public function __set($attributeName, $attributeValue)
    {
        throw new Exception('Attribute modification is not allowed.');
    }

    /**
     * Magical attributes getter for mapping presenter attributes to original model attributes.
     *
     * @param string $attributeName
     * @return mixed|null
     */
    public function __get($attributeName)
    {
        // If the attribute has a accessor method,
        // we will call that then return what it returns as the value.
        if ($this->hasAttributeAccessor($attributeName)) {
            return $this->getAttributeViaAccessor($attributeName);
        }

        // If the attribute exists in the attributes map array,
        // we will return mapped attribute from the original model as the value.
        if ($this->hasAttributeInMap($attributeName)) {
            return $this->getAttributeViaMap($attributeName);
        }

        // Otherwise, we will return null value as a default value of an attribute.
        return null;
    }

    /**
     * Determine if an accessor exists for an attribute.
     *
     * @param string $attributeName
     * @return bool
     * @internal param string $key
     */
    protected function hasAttributeAccessor(string $attributeName)
    {
        return method_exists($this, 'get' . Str::studly($attributeName) . 'Attribute');
    }

    /**
     * Get the value of an attribute using its accessor.
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
    protected function hasAttributeInMap(string $attributeName)
    {
        return key_exists($attributeName, $this->getAttributesMap());
    }

    /**
     * Get the value of an original attribute using the attributes map.
     *
     * @param string $attributeName
     * @return bool
     */
    protected function getAttributeViaMap(string $attributeName)
    {
        return $this->originalModel->{$this->getAttributesMap()[$attributeName]};
    }

    /**
     * Convert the model to its string representation.
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
    public function toJson($options = 0)
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
