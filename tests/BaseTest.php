<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
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
            'username' => 'anna',
            'password' => 'password',
            'first_name' => 'Anna',
            'last_name' => 'P.',
            'role' => [
                'name' => 'User',
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
