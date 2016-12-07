<?php

use PHPUnit\Framework\TestCase;
use Tooleks\Laravel\Presenter\ModelPresenter;

/**
 * Class ModelPresenterTest
 */
class ModelPresenterTest extends TestCase
{
    /**
     * Provide user model instance.
     *
     * @return User
     */
    protected function provideUserModel()
    {
        return new User([
            'username' => 'anna',
            'password' => 'password',
            'first_name' => 'Anna',
            'last_name' => 'P.',
        ]);
    }

    /**
     * Test initialization.
     */
    public function testInitialization()
    {
        try {
            $userPresenter = new UserPresenter((object)[]); // Passing invalid object type as an original model.
            $initialized = true;
        } catch (Throwable $e) {
            $initialized = false;
        }

        $this->assertTrue($initialized === false);

        try {
            $userPresenter = new UserPresenter($this->provideUserModel()); // Passing valid object type as an original model.
            $initialized = true;
        } catch (Throwable $e) {
            $initialized = false;
        }

        $this->assertTrue($initialized === true);
        $this->assertTrue($userPresenter instanceof ModelPresenter);
    }

    /**
     * Test set attributes.
     */
    public function testSetAttribute()
    {
        $user = $this->provideUserModel();

        $userPresenter = new UserPresenter($user);

        $userPresenter->password = 'password';

        $this->assertTrue($userPresenter->password === null);
    }

    /**
     * Test get attributes.
     */
    public function testGetAttribute()
    {
        $user = $this->provideUserModel();

        $userPresenter = new UserPresenter($user);

        $this->assertTrue($userPresenter->name === $user->username);
        $this->assertTrue($userPresenter->password === null);
        $this->assertTrue($userPresenter->first_name === $user->first_name);
        $this->assertTrue($userPresenter->last_name === $user->last_name);
    }

    /**
     * Test attributes override.
     */
    public function testAttributesOverride()
    {
        $user = $this->provideUserModel();

        $userPresenter = new UserPresenter($user);

        $this->assertTrue($userPresenter->full_name === $user->first_name . ' ' . $user->last_name);
    }

    /**
     * Test toArray() method.
     */
    public function testToArrayMethod()
    {
        $user = $this->provideUserModel();

        $userPresenter = new UserPresenter($user);

        $arrayFromToArrayMethod = $userPresenter->toArray();

        $this->assertTrue(is_array($arrayFromToArrayMethod));

        $userPresenterReflector = new ReflectionObject($userPresenter);
        $getMapMethod = $userPresenterReflector->getMethod('getAttributesMap');
        $getMapMethod->setAccessible(true);

        $arrayFromGetMapMethod = $getMapMethod->invoke($userPresenter); // Call protected 'getAttributesMap()' method.

        $this->assertTrue(array_diff_key($arrayFromToArrayMethod, $arrayFromGetMapMethod) === []);

        foreach ($arrayFromToArrayMethod as $attributeName => $value) {
            $this->assertTrue($value === $userPresenter->{$attributeName});
        }
    }

    /**
     * Test jsonSerialize() method.
     */
    public function testJsonSerializeMethod()
    {
        $user = $this->provideUserModel();

        $userPresenter = new UserPresenter($user);

        $this->assertTrue($userPresenter->jsonSerialize() === $userPresenter->toArray());
    }

    /**
     * Test toJson() method.
     */
    public function testToJsonMethod()
    {
        $user = $this->provideUserModel();

        $userPresenter = new UserPresenter($user);

        $this->assertTrue(json_decode($userPresenter->toJson()) !== null);
    }
}
