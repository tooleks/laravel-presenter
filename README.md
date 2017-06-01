# The Laravel Presenter Package

The package provides the `Presenter` layer for wrapping model objects into new presentations.

## Features

The package supports:

* Objects and arrays presentation
* Nested attributes
* Attributes overriding
* The Laravel 5.2 collections
* JSON serialization
* Casting to array/JSON
* Service injections into the constructor

## Requirements

"php": "^7.0",
"illuminate/support": "^5.2",
"illuminate/contracts": "^5.2"

## Installation

### Package Installation

Execute the following command to get the latest version of the package:

```shell
composer require tooleks/laravel-presenter
```

### App Configuration

To register the service provider simply add the `Tooleks\Laravel\Presenter\Providers\PresenterProvider::class` into your `config/app.php` to the end of the `providers` array:

```php
'providers' => [
    ...
    Tooleks\Laravel\Presenter\Providers\PresenterProvider::class,
],
```


## Usage Examples

### Model Presentation

To define your presenter class, you need to extend base `Tooleks\Laravel\Presenter\Presenter` class, as shown in the example below.

Override the `getAttributesMap()` method to build a map for presenter to wrapped model attributes.

```php
<?php

namespace App\Presenters;

use Tooleks\Laravel\Presenter\Presenter;

/**
 * Class UserPresenter.
 *
 * @property string nickname
 * @property string short_name
 * @property string full_name
 * @property string role
 */
class UserPresenter extends Presenter
{
    /**
     * @inheritdoc
     */
    protected function getAttributesMap(): array
    {
        return [
            // 'presenter_attribute_name' => 'wrapped_model_attribute_name'
            'nickname' => 'username',
            'short_name' => 'first_name',
            'full_name' => function () {
                return $this->getWrappedModelAttribute('first_name') . ' ' . $this->getWrappedModelAttribute('last_name');
            },
            'role' => 'role.name',
        ];
    }
}
```

Create a presenter object instance by passing a wrapped model into a `setWrappedModel` method and use it like an object with `nickname`, `short_name`, `full_name`, `role` attributes. The wrapped model may be an `array` or an `object`.

```php
<?php

use App\Presenters\UserPresenter;

$userArray = [ 
    'username' => 'anna',
    'first_name' => 'Anna',
    'last_name' => 'P.',
    'role' => [
        'name' => 'User',
    ],
];

$userObject = (object)$dataArray;

$userPresenter = app()->make(UserPresenter::class)->setWrappedModel($userArray);
// Create the presenter from the wrapped model array.

$userPresenter = app()->make(UserPresenter::class)->setWrappedModel($userObject);
// Create the presenter from the wrapped model object.

echo $userPresenter->nickname;
// Prints 'anna' string, as we mapped the wrapped model 'username' attribute to the presenter 'nickname' attribute.
echo $userPresenter->short_name;
// Prints 'Anna' string, as we mapped the wrapped model 'first_name' attribute to the presenter 'short_name' attribute.
echo $userPresenter->full_name;
// Prints 'Anna P.' string, as we override the presenter 'full_name' attribute by the anonymous function.
echo $userPresenter->role;
// Prints 'User' string, as we mapped the wrapped model 'role.name' nested attribute to the presenter 'role' attribute.
```

### Collection Presentation

The package also provides collection macros method `present()` for wrapping each item in the collection into a presenter class.

```php
<?php

use App\Presenters\UserPresenter;

collect([$userArray, $userObject])->present(UserPresenter::class);
// Create the collection of the 'UserPresenter' items.
```

## Advanced Usage Examples

The `Tooleks\Laravel\Presenter\Presenter` class implements `Illuminate\Contracts\Support\Arrayable`, `Illuminate\Contracts\Support\Jsonable`, `JsonSerializable` interfaces, so you may pass objects of this class directly into the response.

```php
<?php

use App\Presenters\UserPresenter;

$user = \App\User::find(1);

return response(app()->make(UserPresenter::class)->setWrappedModel($user));
```

## Tests

Execute the following command to run tests:

```shell
./vendor/bin/phpunit
```
