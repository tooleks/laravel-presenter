<?php

use Tooleks\Laravel\Presenter\Presenter;

/**
 * Class UserPresenter.
 *
 * @property string name
 * @property string first_name
 * @property string last_name
 * @property string full_name
 * @property string role
 */
class UserPresenter extends Presenter
{
    /**
     * @inheritdoc
     */
    protected function getAttributesMap() : array
    {
        return [
            // 'presenter_attribute_name' => 'presentee_attribute_name'
            'name' => 'username',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'full_name' => function () {
                return $this->getPresenteeAttribute('first_name') . ' ' . $this->getPresenteeAttribute('last_name');
            },
            'role' => 'role.name',
        ];
    }
}
