# The Laravel 5 Presenter Package

This package provides abstract `ModelPresenter` and `CollectionPresenter` classes. 

It is like a `view model` class that wraps original model/collection object into a new presentation.

## Requirements

PHP ~7.0, Laravel ~5.0.

## Installation

### Package Installation

Execute the following command to get the latest version of the package:

```shell
composer require tooleks/laravel-presenter
```

## Basic Usage Examples

### Model Presenter

Used for a presentation of the model entity.

To define your model presenter class, you need to extend base `\Tooleks\Laravel\Presenter\ModelPresenter` class, as shown in the example below.

Override `getOriginalModelClass()` method to provide an original model class name you want to represent.

Override the `getAttributesMap()` method to create a map for presenter-to-model attributes. Also, you can override the mapping defined in the `getAttributesMap()` method with the mutator method (see the `full_name` attribute and the `getFullNameAttribute()` mutator method in the example below).

```php
<?php

namespace App\Presenters;

use Tooleks\Laravel\Presenter\ModelPresenter;

/**
 * Class UserPresenter
 * @attribute string name
 * @attribute string first_name
 * @attribute string last_name
 * @attribute string full_name
 */
class UserPresenter extends ModelPresenter
{
    /**
     * @inheritdoc
     */
    protected function getOriginalModelClass() : string
    {
        return \App\User::class;
    }

    /**
     * @inheritdoc
     */
    protected function getAttributesMap() : array
    {
        return [
            'name' => 'username', // Attribute 'username' is mapped to 'name' attribute.
            'first_name' => 'first_name',  // Attribute 'first_name' is mapped to 'first_name' attribute.
            'last_name' => 'last_name',  // Attribute 'last_name' is mapped to 'last_name' attribute.
            'full_name' => 'full_name', // Attribute 'full_name' is overridden in the 'getFullNameAttribute()' method.
        ];
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->originalModel->first_name . ' ' . $this->originalModel->last_name;
    }
}

```

Create a presenter object and use it like an object with `name`, `first_name`, `last_name`, `full_name` attributes.

```php
<?php

$user = new \App\User();

$user->username = 'anna';
$user->first_name = 'Anna';
$user->last_name = 'P.';

$userPresenter = new \App\Presenters\UserPresenter($user);

echo $userPresenter->name; // Prints 'anna' string, as we mapped '\App\User' 'username' attribute to '\App\Presenters\UserPresenter' 'name' attribute.
echo $userPresenter->first_name; // Prints 'Anna' string, as we mapped '\App\User' 'first_name' attribute to '\App\Presenters\UserPresenter' 'first_name' attribute.
echo $userPresenter->full_name; // Prints 'Anna P.' string, as we override '\App\Presenters\UserPresenter' 'full_name' attribute with the 'getFullNameAttribute()' method.

```

### Collection Presenter

Used for a presentation of the model collection.

To define your collection presenter class, you need to extend base `\Tooleks\Laravel\Presenter\CollectionPresenter` class, as shown in the example below.

Override `getModelPresenterClass()` method to provide a model presenter class name you want to collect.

```php
<?php

namespace App\Presenters;

use Tooleks\Laravel\Presenter\CollectionPresenter;

/**
 * Class UserCollectionPresenter
 */
class UserCollectionPresenter extends CollectionPresenter
{
    /**
     * @inheritdoc
     */
    protected function getModelPresenterClass() : string
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

`\Tooleks\Laravel\Presenter\ModelPresenter` and `\Tooleks\Laravel\Presenter\CollectionPresenter` classes implements `Arrayable`, `Jsonable`, `JsonSerializable` interfaces, so you may pass objects of these classes directly into the response. This may be helpful when you develop REST API.

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
