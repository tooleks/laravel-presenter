<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use Tooleks\Laravel\Presenter\Providers\PresenterProvider;

/**
 * Class BaseTest.
 */
class BaseTest extends TestCase
{
    /**
     * @inheritdoc
     */
    public function createApplication()
    {
        $app = new Application;

        // PresenterProvider class registration for 'Collection::present()' method testing.
        $app->register(PresenterProvider::class);

        $app->boot();

        return $app;
    }

    /**
     * @return array
     */
    public function testModelProvider()
    {
        $sample = [
            'plain_attribute' => 'plain_attribute_value',
            'nested' => [
                'attribute' => 'nested_attribute_value',
            ],
        ];

        return [
            [
                $sample,
                TestPresenter::class,
                [
                    'plain' => 'plain_attribute_value',
                    'nested' => 'nested_attribute_value',
                    'callable' => 'plain_attribute_value' . ' ' . 'nested_attribute_value',
                ],
            ],
            [
                json_decode(json_encode($sample)), // Hack to recursively cast an array to an object.
                TestPresenter::class,
                [
                    'plain' => 'plain_attribute_value',
                    'nested' => 'nested_attribute_value',
                    'callable' => 'plain_attribute_value' . ' ' . 'nested_attribute_value',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function testCollectionProvider()
    {
        return [
            [
                collect([$this->testModelProvider()[0][0], $this->testModelProvider()[1][0]]),
            ],
        ];
    }
}
