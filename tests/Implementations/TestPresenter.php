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
    protected function getAttributesMap(): array
    {
        return [
            // 'presenter_attribute_name' => 'wrapped_model_attribute_name'
            'plain' => 'plain_attribute',
            'nested' => 'nested.attribute',
            'callable' => function () {
                return $this->getWrappedModelAttribute('plain_attribute') . ' ' . $this->getWrappedModelAttribute('nested.attribute');
            },
        ];
    }
}
