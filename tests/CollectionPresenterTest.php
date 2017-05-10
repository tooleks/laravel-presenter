<?php

use Tooleks\Laravel\Presenter\Presenter;
use Illuminate\Support\Collection;

/**
 * Class CollectionPresenterTest.
 */
class CollectionPresenterTest extends BaseTest
{
    /**
     * Test collection present method.
     *
     * @dataProvider testCollectionProvider
     * @param Collection $collection
     */
    public function testCollectionPresentMethod($collection)
    {
        $collection
            ->present(TestPresenter::class)
            ->map(function ($userPresenter) {
                $this->assertInstanceOf(Presenter::class, $userPresenter);
            });
    }
}
