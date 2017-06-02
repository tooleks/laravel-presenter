<?php

use Illuminate\Support\Collection;
use Tooleks\Laravel\Presenter\Presenter;

/**
 * Class CollectionPresenterTest.
 */
class CollectionPresenterTest extends BaseTest
{
    /**
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
