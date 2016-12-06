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
        try {
            $userCollectionPresenter = new UserCollectionPresenter($this->provideUserCollection());
            $initialized = true;
        } catch (Exception $e) {
            $initialized = false;
        }

        $this->assertTrue($initialized === true);
        $this->assertTrue($userCollectionPresenter instanceof CollectionPresenter);

        $userCollectionPresenter->map(function ($modelPresenter) {
            $this->assertTrue($modelPresenter instanceof ModelPresenter);
        });
    }
}
