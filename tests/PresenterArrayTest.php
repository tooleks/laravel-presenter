<?php

use Tooleks\Laravel\Presenter\Presenter;

/**
 * Class PresenterArrayTest.
 */
class PresenterArrayTest extends BaseTest
{
    /**
     * Test initialization.
     */
    public function testInitialization()
    {
        $userPresenter = new UserPresenter($this->provideTestArray());

        $this->assertInstanceOf(Presenter::class, $userPresenter);
    }


    /**
     * Test presenter initialization with invalid presentee type.
     */
    public function testPresenteeInvalidInitialization()
    {
        $this->expectException(InvalidArgumentException::class);

        new UserPresenter('string');
    }

    /**
     * Test set attribute.
     */
    public function testSetAttribute()
    {
        $userPresenter = new UserPresenter($this->provideTestArray());

        $this->expectException(LogicException::class);

        $userPresenter->first_name = 'Anna';

        $this->expectException(LogicException::class);

        $userPresenter->password = 'password';
    }

    /**
     * Test get attributes.
     */
    public function testGetAttribute()
    {
        $user = $this->provideTestArray();

        $userPresenter = new UserPresenter($user);

        $this->assertEquals($userPresenter->name, $user['username']);
        $this->assertEquals($userPresenter->password, null);
        $this->assertEquals($userPresenter->first_name, $user['first_name']);
        $this->assertEquals($userPresenter->last_name, $user['last_name']);
    }

    /**
     * Test attributes override.
     */
    public function testAttributesOverride()
    {
        $user = $this->provideTestArray();

        $userPresenter = new UserPresenter($user);

        $this->assertEquals($userPresenter->full_name, $user['first_name'] . ' ' . $user['last_name']);
    }

    /**
     * Test toArray() method.
     */
    public function testToArrayMethod()
    {
        $user = $this->provideTestArray();

        $userPresenter = new UserPresenter($user);

        $arrayFromToArrayMethod = $userPresenter->toArray();

        $this->assertInternalType('array', $arrayFromToArrayMethod);

        $getMapMethod = (new ReflectionObject($userPresenter))->getMethod('getAttributesMap');
        $getMapMethod->setAccessible(true);

        $arrayFromGetMapMethod = $getMapMethod->invoke($userPresenter); // Call protected 'getAttributesMap()' method.

        $this->assertEquals(array_diff_key($arrayFromToArrayMethod, $arrayFromGetMapMethod), []);

        foreach ($arrayFromToArrayMethod as $attributeName => $value) {
            $this->assertEquals($value, $userPresenter->{$attributeName});
        }
    }

    /**
     * Test jsonSerialize() method.
     */
    public function testJsonSerializeMethod()
    {
        $userPresenter = new UserPresenter($this->provideTestArray());

        $this->assertEquals($userPresenter->jsonSerialize(), $userPresenter->toArray());
    }

    /**
     * Test toJson() method.
     */
    public function testToJsonMethod()
    {
        $user = $this->provideTestArray();

        $userPresenter = new UserPresenter($user);

        $this->assertNotEquals(json_decode($userPresenter->toJson()), null);
    }
}
