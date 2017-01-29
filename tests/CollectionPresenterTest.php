<?php

use Tooleks\Laravel\Presenter\Presenter;

/**
 * Class CollectionPresenterTest.
 */
class CollectionPresenterTest extends BaseTest
{
    /**
     * Test collection present method.
     */
    public function testCollectionPresentMethod()
    {
        $this->provideTestCollection()
            ->present(UserPresenter::class)
            ->map(function ($userPresenter) {
                $this->assertInstanceOf(Presenter::class, $userPresenter);
            });
    }
}
