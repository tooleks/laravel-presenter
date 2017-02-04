# The Laravel 5 Presenter Package

The package provides abstract `Presenter` class for wrapping model objects into new presentations.

## Features

The package supports:

* Objects and arrays presentation
* Nested attributes
* Attributes overriding
* The Laravel 5.2 collections
* JSON serialization
* Casting to array/JSON

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

Override the `getAttributesMap()` method to build a map for presenter-to-presentee attributes.

```php
<?php

use Tooleks\Laravel\Presenter\Presenter;

/**
 * Class UserPresenter.
 *
 * @property string name
 * @property string first_name
 * @property string last_name
 * @property string full_name
 * @property string role
 */
class UserPresenter extends Presenter
{
    /**
     * @inheritdoc
     */
    protected function getAttributesMap() : array
    {
        return [
            // 'presenter_attribute_name' => 'presentee_attribute_name'
            'name' => 'username',           // The presentee 'username' attribute mapped to presenter 'name' attribute.
            'first_name' => 'first_name',   // The presentee 'first_name' attribute mapped to presenter 'first_name' attribute.
            'last_name' => 'last_name',     // The presentee 'last_name' attribute mapped to presenter 'last_name' attribute.
            'full_name' => function () {
                return $this->getPresenteeAttribute('first_name') . ' ' . $this->getPresenteeAttribute('last_name');
            },                              // The presenter 'full_name' attribute overridden by the anonymous function.
            'role' => 'role.name',          // The presentee 'role.name' nested attribute mapped to presenter 'role' attribute.
        ];
    }
}
```

Create a presenter object instance by passing a presentee model into a constructor and use it like an object with `name`, `first_name`, `last_name`, `full_name`, `role` attributes.

Note: Presentee model may be an `array` or an `object`.

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

$userPresenter = new UserPresenter($userArray); // Create presenter from presentee array.

echo $userPresenter->name;          // Prints 'anna' string, as we mapped presentee 'username' attribute to presenter 'name' attribute.
echo $userPresenter->first_name;    // Prints 'Anna' string, as we mapped presentee 'first_name' attribute to presenter 'first_name' attribute.
echo $userPresenter->full_name;     // Prints 'Anna P.' string, as we override presenter 'full_name' attribute.
echo $userPresenter->role;          // Prints 'User' string, as we mapped presentee 'role.name' nested attribute to presenter 'role' attribute.
```

```php
<?php

use App\Presenters\UserPresenter;

$userObject = (object)[
    'username' => 'anna',
    'first_name' => 'Anna',
    'last_name' => 'P.',
    'role' => [
        'name' => 'User',
    ],
];

$userPresenter = new UserPresenter($userObject); // Create presenter from presentee object.

echo $userPresenter->name;          // Prints 'anna' string, as we mapped presentee 'username' attribute to presenter 'name' attribute.
echo $userPresenter->first_name;    // Prints 'Anna' string, as we mapped presentee 'first_name' attribute to presenter 'first_name' attribute.
echo $userPresenter->full_name;     // Prints 'Anna P.' string, as we override presenter 'full_name' attribute.
echo $userPresenter->role;          // Prints 'User' string, as we mapped presentee 'role.name' nested attribute to presenter 'role' attribute.
```

### Collection Presentation

The package also provides collection macros method `present()` for wrapping each item in the collection into a presenter class.

```php
<?php

use Illuminate\Support\Collection;
use App\Presenters\UserPresenter;
use App\User;

$userCollection = new Collection([
    new User([
        'username' => 'anna',
        'first_name' => 'Anna',
        'last_name' => 'P.',
    ]),
    new User([
        'username' => 'anna',
        'first_name' => 'Anna',
        'last_name' => 'P.',
    ]),
]); // A collection of the 'User' items.

$userCollection->present(UserPresenter::class); // A collection of the 'UserPresenter' items.
```

## Advanced Usage Examples

The `Tooleks\Laravel\Presenter\Presenter` class implements `Illuminate\Contracts\Support\Arrayable`, `Illuminate\Contracts\Support\Jsonable`, `JsonSerializable` interfaces, so you may pass objects of this class directly into the response.

```php
<?php

use App\Presenters\UserPresenter;
use App\User;

$user = User::find(1);

return response(new UserPresenter($user));
```
