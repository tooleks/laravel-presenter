<?php

use Tooleks\Laravel\Presenter\{
    Contracts\InvalidArgumentException,
    Contracts\PresenterException,
    Presenter
};

/**
 * Class PresenterObjectTest.
 */
class PresenterObjectTest extends BaseTest
{
    /**
     * Test initialization.
     *
     * @dataProvider testObjectProvider
     * @param object $object
     */
    public function testInitialization($object)
    {
        $testPresenter = new TestPresenter($object);

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
     * @dataProvider testObjectProvider
     * @param object $object
     */
    public function testSetAttribute($object)
    {
        $testPresenter = new TestPresenter($object);

        $this->expectException(PresenterException::class);

        $testPresenter->plain = 'Anna';

        $this->expectException(PresenterException::class);

        $testPresenter->not_existing = 'not_existing_value';
    }

    /**
     * Test get attributes.
     *
     * @dataProvider testObjectProvider
     * @param object $object
     */
    public function testGetAttribute($object)
    {
        $testPresenter = new TestPresenter($object);

        $this->assertEquals($testPresenter->plain, $object->plain_attribute);
        $this->assertEquals($testPresenter->nested, $object->nested->attribute);
        $this->assertEquals($testPresenter->callable, $object->plain_attribute . ' ' . $object->nested->attribute);
        $this->assertEquals($testPresenter->not_existing, null);
    }

    /**
     * Test toArray() method.
     *
     * @dataProvider testObjectProvider
     * @param object $object
     */
    public function testToArrayMethod($object)
    {
        $testPresenter = new TestPresenter($object);

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
     * @dataProvider testObjectProvider
     * @param object $object
     */
    public function testJsonSerializeMethod($object)
    {
        $testPresenter = new TestPresenter($object);

        $this->assertEquals($testPresenter->jsonSerialize(), $testPresenter->toArray());
    }

    /**
     * Test toJson() method.
     *
     * @dataProvider testObjectProvider
     * @param object $object
     */
    public function testToJsonMethod($object)
    {
        $testPresenter = new TestPresenter($object);

        $this->assertNotEquals(json_decode($testPresenter->toJson()), null);
    }
}
