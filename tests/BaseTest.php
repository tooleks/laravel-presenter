<?php

use Illuminate\Foundation\{
    Application,
    Testing\TestCase
};
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
     * Test presenter provider registration.
     */
    public function testPresenterProvider()
    {
        $presenterProvider = $this->app->getProvider(PresenterProvider::class);

        $this->assertInstanceOf(PresenterProvider::class, $presenterProvider);
    }

    /**
     * Test array provider.
     *
     * @return array
     */
    public function testArrayProvider()
    {
        return [
            [[
                'plain_attribute' => 'plain_attribute_value',
                'nested' => [
                    'attribute' => 'nested_attribute_value',
                ],
            ]],
        ];
    }

    /**
     * Test object provider.
     *
     * @return array
     */
    public function testObjectProvider()
    {
        return [
            // Hack to recursively cast an array to an object.
            [json_decode(json_encode($this->testArrayProvider()[0][0]))],
        ];
    }

    /**
     * Test collection provider.
     *
     * @return array
     */
    public function testCollectionProvider()
    {
        return [
            [collect([
                $this->testObjectProvider()[0][0],
                $this->testObjectProvider()[0][0],
                $this->testObjectProvider()[0][0],
            ])],
        ];
    }
}
