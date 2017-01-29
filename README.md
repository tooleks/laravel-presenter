# The Laravel 5 Presenter Package

The package provides abstract `Presenter` class for wrapping model objects into new presentations. This may be useful when building API or passing parameters into views.

## Requirements

"php": "~7.0",
"illuminate/support": "^5.2",
"illuminate/contracts": "^5.2"

## Installation

### Package Installation

Execute the following command to get the latest version of the package:

```shell
composer require tooleks/laravel-presenter
```

### App Configuration

To register the service provider simply add the `\Tooleks\Laravel\Presenter\Providers\PresenterProvider::class` into your `config/app.php` to the end of the `providers` array:

```php
'providers' => [
    ...
    \Tooleks\Laravel\Presenter\Providers\PresenterProvider::class,
],
```


## Usage Examples

### Model Presentation

To define your presenter class, you need to extend base `\Tooleks\Laravel\Presenter\Presenter` class, as shown in the example below.

Override the `Presenter::getAttributesMap()` method to build a map for presenter-to-presentee attributes.

Also, you can override the mapping defined in the `Presenter::getAttributesMap()` method with the accessor methods (see the `full_name` attribute and the `UserPresenter::getFullNameAttribute()` accessor method in the example below).

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
            'name' => 'username',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'full_name' => null,
        ];
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->getPresenteeAttribute('first_name') . ' ' . $this->getPresenteeAttribute('last_name');
    }
}

```

Create a presenter object instance by passing presentee model into a constructor and use it like an object with `name`, `first_name`, `last_name`, `full_name` attributes.

Note: Presentee model may be an `array` or an `object`.

```php
<?php

$userPresenter = new \App\Presenters\UserPresenter([ // Create presenter from presentee array.
    'username' => 'anna',
    'first_name' => 'Anna',
    'last_name' => 'P.',
]);

echo $userPresenter->name; // Prints 'anna' string, as we mapped 'username' attribute to '\App\Presenters\UserPresenter::$name' attribute.
echo $userPresenter->first_name; // Prints 'Anna' string, as we mapped 'first_name' attribute to '\App\Presenters\UserPresenter::$first_name' attribute.
echo $userPresenter->full_name; // Prints 'Anna P.' string, as we override 'full_name' attribute with the '\App\Presenters\UserPresenter::getFullNameAttribute()' method.

```

```php
<?php

$user = new \App\User();

$user->username = 'anna';
$user->first_name = 'Anna';
$user->last_name = 'P.';

$userPresenter = new \App\Presenters\UserPresenter($user); // Create presenter from presentee object.

echo $userPresenter->name; // Prints 'anna' string, as we mapped '\App\User::$username' attribute to '\App\Presenters\UserPresenter::$name' attribute.
echo $userPresenter->first_name; // Prints 'Anna' string, as we mapped '\App\User::$first_name' attribute to '\App\Presenters\UserPresenter::$first_name' attribute.
echo $userPresenter->full_name; // Prints 'Anna P.' string, as we override '\App\Presenters\UserPresenter::$full_name' attribute with the '\App\Presenters\UserPresenter::getFullNameAttribute()' method.

```

### Collection Presentation

The package also provides collection macros method `\Illuminate\Support\Collection::present()` for wrapping each item in the collection into a presenter class.

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

$userCollection->present(\App\Presenters\UserPresenter::class); // A collection of the '\App\Presenters\UserPresenter' items.

```

## Advanced Usage Examples

`\Tooleks\Laravel\Presenter\Presenter` class implements `Arrayable`, `Jsonable`, `JsonSerializable` interfaces, so you may pass objects of this class directly into the response.

```php
<?php

$user = \App\User::find(1);

return response(new \App\Presenters\UserPresenter($user));

```
