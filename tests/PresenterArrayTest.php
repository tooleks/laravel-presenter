<?php

use Tooleks\Laravel\Presenter\{
    Contracts\InvalidArgumentException,
    Contracts\PresenterException,
    Presenter
};

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
        $testPresenter = new TestPresenter($this->provideTestArray());

        $this->assertInstanceOf(Presenter::class, $testPresenter);
    }

    /**
     * Test presenter initialization with invalid presentee type.
     */
    public function testPresenteeInvalidInitialization()
    {
        $this->expectException(InvalidArgumentException::class);

        new TestPresenter('string');
    }

    /**
     * Test set attribute.
     */
    public function testSetAttribute()
    {
        $testPresenter = new TestPresenter($this->provideTestArray());

        $this->expectException(PresenterException::class);

        $testPresenter->plain = 'Anna';

        $this->expectException(PresenterException::class);

        $testPresenter->not_existing = 'not_existing_value';
    }

    /**
     * Test get attributes.
     */
    public function testGetAttribute()
    {
        $test = $this->provideTestArray();

        $testPresenter = new TestPresenter($test);

        $this->assertEquals($testPresenter->plain, $test['plain_attribute']);
        $this->assertEquals($testPresenter->nested, $test['nested']['attribute']);
        $this->assertEquals($testPresenter->callable, $test['plain_attribute'] . ' ' . $test['nested']['attribute']);
        $this->assertEquals($testPresenter->not_existing, null);
    }

    /**
     * Test toArray() method.
     */
    public function testToArrayMethod()
    {
        $test = $this->provideTestArray();

        $testPresenter = new TestPresenter($test);

        $arrayFromToArrayMethod = $testPresenter->toArray();

        $this->assertInternalType('array', $arrayFromToArrayMethod);

        $getMapMethod = (new ReflectionObject($testPresenter))->getMethod('getAttributesMap');
        $getMapMethod->setAccessible(true);

        $arrayFromGetMapMethod = $getMapMethod->invoke($testPresenter); // Call protected 'getAttributesMap()' method.

        $this->assertInternalType('array', $arrayFromGetMapMethod);
        $this->assertEquals(array_diff_key($arrayFromToArrayMethod, $arrayFromGetMapMethod), []);

        foreach ($arrayFromToArrayMethod as $attributeName => $value) {
            $this->assertEquals($value, $testPresenter->{$attributeName});
        }
    }

    /**
     * Test jsonSerialize() method.
     */
    public function testJsonSerializeMethod()
    {
        $testPresenter = new TestPresenter($this->provideTestArray());

        $this->assertEquals($testPresenter->jsonSerialize(), $testPresenter->toArray());
    }

    /**
     * Test toJson() method.
     */
    public function testToJsonMethod()
    {
        $test = $this->provideTestArray();

        $testPresenter = new TestPresenter($test);

        $this->assertNotEquals(json_decode($testPresenter->toJson()), null);
    }
}
