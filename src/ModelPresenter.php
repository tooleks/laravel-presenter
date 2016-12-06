<?php

namespace Tooleks\LaravelPresenter;

use Exception;
use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class ModelPresenter
 * @package App\Presenters
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

        $this->getValidatedOriginalModelClassType();
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
     * Get the array map of presenter properties mapped to model properties.
     *
     * Override this method to build properties map.
     *
     * Example: return [
     *     ...
     *     'some_presenter_property_name' => 'some_model_property_name',
     *     ...
     * ];
     *
     * Note: You may override presenter property value by creating a method
     * inside the presenter class with the same name as the property name.
     *
     * @return array
     */
    abstract protected function getMap() : array;

    /**
     * Get validated original model class type.
     *
     * @return string
     * @throws Exception
     */
    protected function getValidatedOriginalModelClassType()
    {
        $originalModelClass = $this->getOriginalModelClass();

        if (!class_exists($originalModelClass)) {
            throw new Exception("'{$originalModelClass}' class does not exist.");
        }

        if (!($this->originalModel instanceof $originalModelClass)) {
            throw new Exception("The original model should be a '{$originalModelClass}' class type.");
        }

        return $originalModelClass;
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
     * Magical getter for mapping presenter properties to original model properties.
     *
     * @param string $propertyName
     * @return mixed|null
     */
    public function __get(string $propertyName)
    {
        $methodName = $propertyName;
        if (method_exists($this, $methodName)) {
            return $this->{$methodName}();
        }

        $methodName = str_replace('_', '', $propertyName);
        if (method_exists($this, $methodName)) {
            return $this->{$methodName}();
        }

        if (key_exists($propertyName, $this->getMap())) {
            $property = $this->getMap()[$propertyName];
            return $this->originalModel->{$property};
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

        $properties = array_keys($this->getMap());
        foreach ($properties as $property) {
            $array[$property] = $this->{$property};
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
