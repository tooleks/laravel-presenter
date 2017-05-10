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
 * Legend
 * Presentee - the model that is presented by a presenter.
 * Presenter - the model that presents a presentee.
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

        $this->setPresentee($presentee);
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
            throw new InvalidArgumentException('Presentee model should be an object or an array.');
        }
    }

    /**
     * Set presentee model.
     *
     * @param array|object $presentee
     * @return void
     */
    protected function setPresentee($presentee)
    {
        $this->presentee = $presentee;
    }

    /**
     * Get presentee model.
     *
     * @return object|array
     */
    public function getPresentee()
    {
        return $this->presentee;
    }

    /**
     * Get the array map of presenter attributes mapped to presentee model attributes.
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
     * Attributes setter.
     *
     * @param string $attribute
     * @param mixed $value
     * @throws PresenterExceptionContract
     */
    public function __set($attribute, $value)
    {
        throw new PresenterException('Attribute modification is not allowed.');
    }

    /**
     * Attributes getter for mapping presenter attributes to presentee model attributes.
     *
     * @param string $attribute
     * @return mixed|null
     */
    public function __get($attribute)
    {
        $presenteeAttribute = $this->getAttributesMap()[$attribute] ?? null;

        if (is_null($presenteeAttribute)) {
            return null;
        }

        if (is_callable($presenteeAttribute)) {
            return call_user_func($presenteeAttribute, $this->presentee);
        }

        if (is_string($attribute) || is_float($attribute) || is_int($attribute) || is_bool($attribute)) {
            return $this->getPresenteeAttribute($presenteeAttribute);
        }

        return null;
    }

    /**
     * Get presentee model attribute.
     *
     * @param string $attribute
     * @return mixed|null
     */
    public function getPresenteeAttribute(string $attribute)
    {
        $value = $this->presentee;

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
