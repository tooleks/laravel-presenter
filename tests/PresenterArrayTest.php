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
     *
     * @dataProvider testArrayProvider
     * @param array $array
     */
    public function testInitialization($array)
    {
        $testPresenter = new TestPresenter($array);

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
     *
     * @dataProvider testArrayProvider
     * @param array $array
     */
    public function testSetAttribute($array)
    {
        $testPresenter = new TestPresenter($array);

        $this->expectException(PresenterException::class);

        $testPresenter->plain = 'Anna';

        $this->expectException(PresenterException::class);

        $testPresenter->not_existing = 'not_existing_value';
    }

    /**
     * Test get attributes.
     *
     * @dataProvider testArrayProvider
     * @param array $array
     */
    public function testGetAttribute($array)
    {
        $testPresenter = new TestPresenter($array);

        $this->assertEquals($testPresenter->plain, $array['plain_attribute']);
        $this->assertEquals($testPresenter->nested, $array['nested']['attribute']);
        $this->assertEquals($testPresenter->callable, $array['plain_attribute'] . ' ' . $array['nested']['attribute']);
        $this->assertEquals($testPresenter->not_existing, null);
    }

    /**
     * Test toArray() method.
     *
     * @dataProvider testArrayProvider
     * @param array $array
     */
    public function testToArrayMethod($array)
    {
        $testPresenter = new TestPresenter($array);

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
     *
     * @dataProvider testArrayProvider
     * @param array $array
     */
    public function testJsonSerializeMethod($array)
    {
        $testPresenter = new TestPresenter($array);

        $this->assertEquals($testPresenter->jsonSerialize(), $testPresenter->toArray());
    }

    /**
     * Test toJson() method.
     *
     * @dataProvider testArrayProvider
     * @param array $array
     */
    public function testToJsonMethod($array)
    {
        $testPresenter = new TestPresenter($array);

        $this->assertNotEquals(json_decode($testPresenter->toJson()), null);
    }
}
