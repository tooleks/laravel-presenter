<?php

use Tooleks\Laravel\Presenter\CollectionPresenter;

/**
 * Class UserCollectionPresenter
 */
class UserCollectionPresenter extends CollectionPresenter
{
    /**
     * @inheritdoc
     */
    protected function getModelPresenterClass() : string
    {
        return UserPresenter::class;
    }
}
