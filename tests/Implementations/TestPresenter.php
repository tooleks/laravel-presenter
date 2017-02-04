<?php

use Tooleks\Laravel\Presenter\Presenter;

/**
 * Class TestPresenter.
 *
 * @property string plain
 * @property string nested
 * @property string callable
 */
class TestPresenter extends Presenter
{
    /**
     * @inheritdoc
     */
    protected function getAttributesMap() : array
    {
        return [
            // 'presenter_attribute_name' => 'presentee_attribute_name'
            'plain' => 'plain_attribute',
            'nested' => 'nested.attribute',
            'callable' => function ($presenter) {
                return $this->getPresenteeAttribute('plain_attribute') . ' ' . $this->getPresenteeAttribute('nested.attribute');
            },
        ];
    }
}
