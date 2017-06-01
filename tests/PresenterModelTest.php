<?php

use Tooleks\Laravel\Presenter\Exceptions\AttributeNotFoundException;
use Tooleks\Laravel\Presenter\Exceptions\InvalidArgumentException;
use Tooleks\Laravel\Presenter\Exceptions\PresenterException;
use Tooleks\Laravel\Presenter\Presenter;

/**
 * Class PresenterModelTest.
 */
class PresenterModelTest extends BaseTest
{
    /**
     * @dataProvider testModelProvider
     * @param array $model
     */
    public function testInitialization($model)
    {
        $testPresenter = $this->app->make(TestPresenter::class)->setWrappedModel($model);

        $this->assertInstanceOf(Presenter::class, $testPresenter);
    }

    public function testInvalidInitialization()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->app->make(TestPresenter::class)->setWrappedModel('invalid');
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     */
    public function testSetExistingAttribute($model)
    {
        $testPresenter = $this->app->make(TestPresenter::class)->setWrappedModel($model);

        $this->expectException(PresenterException::class);

        $testPresenter->plain = 'Anna';
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     */
    public function testSetNotExistingAttribute($model)
    {
        $testPresenter = $this->app->make(TestPresenter::class)->setWrappedModel($model);

        $this->expectException(PresenterException::class);

        $testPresenter->not_existing = 'not_existing_value';
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     */
    public function testGetPlainAttribute($model)
    {
        $testPresenter = $this->app->make(TestPresenter::class)->setWrappedModel($model);

        $this->assertEquals($testPresenter->plain, $model->plain_attribute ?? $model['plain_attribute']);
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     */
    public function testGetNestedAttribute($model)
    {
        $testPresenter = $this->app->make(TestPresenter::class)->setWrappedModel($model);

        $this->assertEquals($testPresenter->nested, $model->nested->attribute ?? $model['nested']['attribute']);
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     */
    public function testGetCallableAttribute($model)
    {
        $testPresenter = $this->app->make(TestPresenter::class)->setWrappedModel($model);

        if (is_object($model)) {
            $callableAttribute = $model->plain_attribute . ' ' . $model->nested->attribute;
        } elseif (is_array($model)) {
            $callableAttribute = $model['plain_attribute'] . ' ' . $model['nested']['attribute'];
        }

        $this->assertEquals($testPresenter->callable, $callableAttribute ?? null);
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     */
    public function testGetNotExistingAttribute($model)
    {
        $testPresenter = $this->app->make(TestPresenter::class)->setWrappedModel($model);

        $this->expectException(AttributeNotFoundException::class);

        $this->assertEquals($testPresenter->not_existing, null);
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     */
    public function testToArrayMethod($model)
    {
        $testPresenter = $this->app->make(TestPresenter::class)->setWrappedModel($model);

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
     */
    public function testJsonSerializeMethod($model)
    {
        $testPresenter = $this->app->make(TestPresenter::class)->setWrappedModel($model);

        $this->assertEquals($testPresenter->jsonSerialize(), $testPresenter->toArray());
    }

    /**
     * @dataProvider testModelProvider
     * @param array $model
     */
    public function testToJsonMethod($model)
    {
        $testPresenter = $this->app->make(TestPresenter::class)->setWrappedModel($model);

        $this->assertNotEquals(json_decode($testPresenter->toJson()), null);
    }
}
