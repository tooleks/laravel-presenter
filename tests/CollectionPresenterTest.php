<?php

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use Tooleks\Laravel\Presenter\CollectionPresenter;
use Tooleks\Laravel\Presenter\ModelPresenter;

/**
 * Class CollectionPresenterTest
 */
class CollectionPresenterTest extends TestCase
{
    /**
     * Provide user model collection.
     *
     * @return Collection
     */
    protected function provideUserCollection()
    {
        return new Collection([
            new User([
                'username' => 'anna',
                'password' => 'password',
                'first_name' => 'Anna',
                'last_name' => 'P.',
            ]),
            new User([
                'username' => 'anna',
                'password' => 'password',
                'first_name' => 'Anna',
                'last_name' => 'P.',
            ]),
        ]);
    }

    /**
     * Test initialization.
     */
    public function testInitialization()
    {
        $userCollectionPresenter = new UserCollectionPresenter($this->provideUserCollection());

        $this->assertInstanceOf(CollectionPresenter::class, $userCollectionPresenter);

        $userCollectionPresenter->each(function ($userPresenter) {
            $this->assertInstanceOf(ModelPresenter::class, $userPresenter);
            $this->assertInstanceOf(UserPresenter::class, $userPresenter);
        });
    }

    /**
     * Test failed initialization.
     */
    public function testFailedInitialization()
    {
        $this->expectException(LogicException::class);

        new UserCollectionPresenter([(object)[]]);
    }
}
