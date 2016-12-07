<?php

namespace Tooleks\Laravel\Presenter;

use Exception;
use Illuminate\Support\Collection;

/**
 * Class CollectionPresenter
 * @package Tooleks\Laravel\Presenter
 * @author Oleksandr Tolochko <tooleks@gmail.com>
 */
abstract class CollectionPresenter extends Collection
{
    /**
     * CollectionPresenter constructor.
     *
     * @param mixed $items
     */
    public function __construct($items = [])
    {
        parent::__construct();

        $this->validateModelPresenterClassType();

        $modelPresenterClass = $this->getModelPresenterClass();

        foreach ($this->getFilteredArrayableItems($items) as $item) {
            $this->push(new $modelPresenterClass($item));
        }
    }

    /**
     * Get model presenter class.
     *
     * @return string
     */
    abstract protected function getModelPresenterClass() : string;

    /**
     * Validate model presenter class type.
     *
     * @return string
     * @throws Exception
     */
    protected function validateModelPresenterClassType()
    {
        $modelPresenterClass = $this->getModelPresenterClass();

        if (!class_exists($modelPresenterClass)) {
            throw new Exception("'{$modelPresenterClass}' class does not exist.");
        }

        return $modelPresenterClass;
    }

    /**
     * Get filtered results array of items from Collection or Arrayable.
     *
     * @param mixed $items
     * @return array
     */
    protected function getFilteredArrayableItems($items) : array
    {
        return array_filter($this->getArrayableItems($items));
    }
}
