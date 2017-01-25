<?php

namespace Tooleks\Laravel\Presenter;

use Illuminate\Support\Collection;
use LogicException;

/**
 * Class CollectionPresenter.
 *
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
        $this->assertModelPresenterClass();

        parent::__construct($this->presentItems($items));
    }

    /**
     * Get model presenter class.
     *
     * @return string
     */
    abstract protected function getModelPresenterClass() : string;

    /**
     * Assert model presenter class.
     *
     * @return void
     * @throws LogicException
     */
    protected function assertModelPresenterClass()
    {
        $modelPresenterClass = $this->getModelPresenterClass();

        if (!class_exists($modelPresenterClass)) {
            throw new LogicException(sprintf('The "%s" class does not exist.'), $modelPresenterClass);
        }
    }

    /**
     * Present collection items.
     *
     * @param mixed $items
     * @return array
     */
    protected function presentItems($items)
    {
        return array_map([$this, 'presentItem'], $this->getArrayableItems($items));
    }

    /**
     * Present collection item.
     *
     * @param mixed $item
     * @return mixed
     */
    protected function presentItem($item)
    {
        $modelPresenterClass = $this->getModelPresenterClass();

        return new $modelPresenterClass($item);
    }
}
