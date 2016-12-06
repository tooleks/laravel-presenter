<?php

use PHPUnit\Framework\TestCase;
use Tooleks\LaravelPresenter\ModelPresenter;

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
     * Test properties.
     */
    public function testProperties()
    {
        $user = $this->provideUserModel();

        $userPresenter = new UserPresenter($user);

        $this->assertTrue($userPresenter->name === $user->username);
        $this->assertTrue($userPresenter->password !== $user->password);
        $this->assertTrue($userPresenter->first_name === $user->first_name);
        $this->assertTrue($userPresenter->last_name === $user->last_name);
    }

    /**
     * Test properties override.
     */
    public function testPropertiesOverride()
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
        $getMapMethod = $userPresenterReflector->getMethod('getMap');
        $getMapMethod->setAccessible(true);

        $arrayFromGetMapMethod = $getMapMethod->invoke($userPresenter); // Call protected 'getMap()' method.

        $this->assertTrue(array_diff_key($arrayFromToArrayMethod, $arrayFromGetMapMethod) === []);

        foreach ($arrayFromToArrayMethod as $propertyName => $value) {
            $this->assertTrue($value === $userPresenter->{$propertyName});
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
