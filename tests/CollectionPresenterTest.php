<?php

use Illuminate\Support\Collection;
use Tooleks\Laravel\Presenter\Presenter;

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
        $collection->present(TestPresenter::class)->each(function ($testPresenter) {
            $this->assertInstanceOf(Presenter::class, $testPresenter);
        });
    }
}
