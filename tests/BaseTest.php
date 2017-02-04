<?php

use Illuminate\Foundation\{
    Application,
    Testing\TestCase
};
use Illuminate\Support\Collection;
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
     * Provide test object.
     *
     * @return object
     */
    protected function provideTestObject()
    {
        return json_decode(json_encode($this->provideTestArray())); // Hack to recursively cast an array to an object.
    }

    /**
     * Provide test array.
     *
     * @return array
     */
    protected function provideTestArray()
    {
        return [
            'plain_attribute' => 'plain_attribute_value',
            'nested' => [
                'attribute' => 'nested_attribute_value',
            ],
        ];
    }

    /**
     * Provide test collection.
     *
     * @return Collection
     */
    protected function provideTestCollection()
    {
        return collect([
            $this->provideTestObject(),
            $this->provideTestObject(),
            $this->provideTestObject(),
        ]);
    }
}
