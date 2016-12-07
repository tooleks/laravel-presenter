<?php

namespace Tooleks\Laravel\Presenter;

use Exception;
use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

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
     * @throws Exception
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
            throw new Exception("'{$originalModelClass}' class does not exist.");
        }

        if (!($this->originalModel instanceof $originalModelClass)) {
            throw new Exception("The original model should be a '{$originalModelClass}' class type.");
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
     * @return void
     */
    public function __set($attributeName, $attributeValue)
    {
        // Adding new attributes dynamically is not allowed.
    }

    /**
     * Magical attributes getter for mapping presenter attributes to original model attributes.
     *
     * @param string $attributeName
     * @return mixed|null
     */
    public function __get($attributeName)
    {
        $methodName = 'get' . str_replace('_', '', $attributeName) . 'attribute';
        if (method_exists($this, $methodName)) {
            return $this->{$methodName}();
        }

        if (key_exists($attributeName, $this->getAttributesMap())) {
            $attribute = $this->getAttributesMap()[$attributeName];
            return $this->originalModel->{$attribute};
        }

        return null;
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
