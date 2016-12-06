# The Laravel 5 Presenter Package

This package provides abstract `ModelPresenter` and `CollectionPresenter` classes. It's like a `view model` classes that wraps original model/collection object into a new presentation.

## Requirements

PHP >= 7.0, Laravel >= 5.0.

## Installation

### Package Installation

Execute the following command to get the latest version of the package:

```shell
composer require tooleks/laravel-presenter
```

## Basic Usage Examples

### Model Presenter

Used for a presentation of the model entity.

To define your model presenter class, you need to extend base `\Tooleks\LaravelPresenter\ModelPresenter` class, as shown in the example below.

Override `getOriginalModelClass()` method to provide an original model class name you want to represent.

Override the `getMap()` method to create a map for presenter-to-model properties. Also, you can override the mapping defined in the `getMap()` method with the method with the same name as the mapped property (see the `full_name` property and the `fullName()` overriding method in the example below, you can use 'camelCase' or 'snake_case' style for properties overriding method names).

```php
<?php

namespace App\Presenters;

use Tooleks\LaravelPresenter\ModelPresenter;

/**
 * Class UserPresenter
 * @property string name
 * @property string full_name
 */
class UserPresenter extends ModelPresenter
{
    /**
     * @inheritdoc
     */
    protected function getOriginalModelClass(): string
    {
        return \App\User::class;
    }

    /**
     * @inheritdoc
     */
    protected function getMap(): array
    {
        return [
            'name' => 'username', // Property 'username' is overriden by 'name' property.
            'full_name' => 'full_name', // Property 'full_name' is overriden in the 'fullName()' method.
        ];
    }

    /**
     * @return string
     */
    public function fullName()
    {
        return $this->originalModel->first_name . ' ' . $this->originalModel->last_name;
    }
}

```

Create a presenter object and use it like an object with `name`, `full_name` properties.

```php
<?php

$user = new \App\User();
$user->username = 'anna';
$user->first_name = 'Anna';
$user->last_name = 'P.';

$userPresenter = new \App\Presenters\UserPresenter($user);

echo $userPresenter->name; // Prints 'anna' string, as we mapped '\App\User' 'username' property to '\App\Presenters\UserPresenter' 'name' property.
echo $userPresenter->full_name; // Prints 'Anna P.' string, as we override '\App\Presenters\UserPresenter' 'full_name' property with the 'fullName()' method.

```

### Collection Presenter

Used for a presentation of the model collection.

To define your collection presenter class, you need to extend base `\Tooleks\LaravelPresenter\CollectionPresenter` class, as shown in the example below.

Override `getModelPresenterClass()` method to provide a model presenter class name you want to collect.

```php
<?php

namespace App\Presenters;

use Tooleks\LaravelPresenter\CollectionPresenter;

/**
 * Class UserCollectionPresenter
 */
class UserCollectionPresenter extends CollectionPresenter
{
    /**
     * @inheritdoc
     */
    protected function getModelPresenterClass(): string
    {
        return \App\Presenters\UserPresenter::class;
    }
}

```

Create a presenter object and use it like a collection of model presenter items.

```php
<?php

$userCollection = new \Illuminate\Support\Collection([
    new \App\User([
        'username' => 'anna',
        'first_name' => 'Anna',
        'last_name' => 'P.',
    ]),
    new \App\User([
        'username' => 'anna',
        'first_name' => 'Anna',
        'last_name' => 'P.',
    ]),
]); // A collection of the '\App\User' items.

$userCollectionPresenter = new \App\Presenters\UserCollectionPresenter($userCollection); // A collection of the '\App\Presenters\UserPresenter' items.

```

## Advanced Usage Examples

`\Tooleks\LaravelPresenter\ModelPresenter` and `\Tooleks\LaravelPresenter\CollectionPresenter` classes implements `Arrayable`, `Jsonable`, `JsonSerializable` interfaces, so you may pass objects of these classes directly into the response. This may be helpful when you develop REST API.

```php
<?php

$user = \App\User::find(1);

return response(new \App\Presenters\UserPresenter($user));

```

```php
<?php

$users = \App\User::all();

return response(new \App\Presenters\UserCollectionPresenter($users));

```
