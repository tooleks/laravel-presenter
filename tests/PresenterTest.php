<?php

use Tooleks\Laravel\Presenter\Exceptions\AttributeNotFoundException;
use Tooleks\Laravel\Presenter\Exceptions\InvalidArgumentException;
use Tooleks\Laravel\Presenter\Exceptions\PresenterException;
use Tooleks\Laravel\Presenter\Presenter;

/**
 * Class PresenterTest.
 */
class PresenterTest extends BaseTest
{
    /**
     * @dataProvider testModelProvider
     * @param array $model
     * @param string $presenterClass
     * @param array $data
     */
    public function testInitialization($model, $presenterClass, $data)
    {
        $testPresenter = $this->app->make($presenterClass)->setWrappedModel($model);

        $this->assertInstanceOf(Presenter::class, $testPresenter);
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     * @param string $presenterClass
     * @param array $data
     */
    public function testInvalidInitialization($model, $presenterClass, $data)
    {
        $this->expectException(InvalidArgumentException::class);

        $this->app->make($presenterClass)->setWrappedModel('invalid');
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     * @param string $presenterClass
     * @param array $data
     */
    public function testSetExistingAttribute($model, $presenterClass, $data)
    {
        $testPresenter = $this->app->make($presenterClass)->setWrappedModel($model);

        $this->expectException(PresenterException::class);

        $testPresenter->{array_keys($data)[0]} = 'Anna';
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     * @param string $presenterClass
     * @param array $data
     */
    public function testSetNotExistingAttribute($model, $presenterClass, $data)
    {
        $testPresenter = $this->app->make($presenterClass)->setWrappedModel($model);

        $this->expectException(PresenterException::class);

        $testPresenter->invalid = 'invalid';
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     * @param string $presenterClass
     * @param array $data
     */
    public function testGetAttribute($model, $presenterClass, $data)
    {
        $testPresenter = $this->app->make($presenterClass)->setWrappedModel($model);

        foreach ($data as $attribute => $value) {
            $this->assertEquals($testPresenter->{$attribute}, $value);
        }
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     * @param string $presenterClass
     * @param array $data
     */
    public function testGetNotExistingAttribute($model, $presenterClass, $data)
    {
        $testPresenter = $this->app->make($presenterClass)->setWrappedModel($model);

        $this->expectException(AttributeNotFoundException::class);

        $testPresenter->invalid;
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     * @param string $presenterClass
     * @param array $data
     */
    public function testGetNotExistingWrappedModelAttribute($model, $presenterClass, $data)
    {
        $testPresenter = $this->app->make($presenterClass)->setWrappedModel($model);

        $this->assertEquals($testPresenter->getWrappedModelAttribute('invalid'), null);
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     * @param string $presenterClass
     * @param array $data
     */
    public function testToArrayMethod($model, $presenterClass, $data)
    {
        $testPresenter = $this->app->make($presenterClass)->setWrappedModel($model);

        $modelFromToArrayMethod = $testPresenter->toArray();

        $this->assertInternalType('array', $modelFromToArrayMethod);

        $getMapMethod = (new ReflectionObject($testPresenter))->getMethod('getAttributesMap');
        $getMapMethod->setAccessible(true);
        $modelFromGetMapMethod = $getMapMethod->invoke($testPresenter); // Call protected 'getAttributesMap()' method.

        $this->assertInternalType('array', $modelFromGetMapMethod);
        $this->assertEquals(array_diff_key($modelFromToArrayMethod, $modelFromGetMapMethod), []);

        foreach ($modelFromToArrayMethod as $attributeName => $value) {
            $this->assertEquals($value, $testPresenter->{$attributeName});
        }
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     * @param string $presenterClass
     * @param array $data
     */
    public function testJsonSerializeMethod($model, $presenterClass, $data)
    {
        $testPresenter = $this->app->make($presenterClass)->setWrappedModel($model);

        $this->assertEquals($testPresenter->jsonSerialize(), $testPresenter->toArray());
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     * @param string $presenterClass
     * @param array $data
     */
    public function testToJsonMethod($model, $presenterClass, $data)
    {
        $testPresenter = $this->app->make($presenterClass)->setWrappedModel($model);

        $this->assertNotEquals(json_decode($testPresenter->toJson()), null);
    }
}
