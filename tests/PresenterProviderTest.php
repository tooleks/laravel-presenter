<?php

use Tooleks\Laravel\Presenter\Providers\PresenterProvider;

/**
 * Class PresenterProviderTest.
 */
class PresenterProviderTest extends BaseTest
{
    public function testPresenterProvider()
    {
        $presenterProvider = $this->app->getProvider(PresenterProvider::class);

        $this->assertInstanceOf(PresenterProvider::class, $presenterProvider);
    }
}
