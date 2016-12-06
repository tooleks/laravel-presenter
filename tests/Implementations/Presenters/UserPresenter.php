<?php

use Tooleks\LaravelPresenter\ModelPresenter;

/**
 * Class UserPresenter
 * @property string name
 * @property string first_name
 * @property string last_name
 * @property string full_name
 */
class UserPresenter extends ModelPresenter
{
    /**
     * @inheritdoc
     */
    protected function getOriginalModelClass() : string
    {
        return User::class;
    }

    /**
     * @inheritdoc
     */
    protected function getMap() : array
    {
        return [
            'name' => 'username',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'full_name' => 'full_name',
        ];
    }

    /**
     * @return string
     */
    public function fullName()
    {
        return $this->originalModel->first_name . ' ' . $this->originalModel->last_name;
    }
}
