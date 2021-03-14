# Automatically cast JSON columns to rich PHP objects in Laravel using Symfony's Serializer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/morrislaptop/laravel-popo-caster.svg?style=flat-square)](https://packagist.org/packages/morrislaptop/laravel-popo-caster)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/morrislaptop/laravel-popo-caster/Tests?label=tests)](https://github.com/morrislaptop/laravel-popo-caster/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/morrislaptop/laravel-popo-caster.svg?style=flat-square)](https://packagist.org/packages/morrislaptop/laravel-popo-caster)

Laravel is awesome. Spatie's [data transfer object](https://github.com/spatie/data-transfer-object) package for PHP is awesome. But they don't cast objects like dates to DateTimes and collections are a bit of pain. Plain Old PHP Objects (POPOs) are a bit better in that regard. 

Have you ever wanted to cast your JSON columns to a value object?

This package gives you a 2 caster classes:

* `Serializer` which serializes your value object and stores it in a single JSON field
* `Normalizer` which normalizes your value object and stores the properties as fields on your model

Under the hood it implements Laravel's [`Castable` interface](https://laravel.com/docs/8.x/eloquent-mutators#castables) with a Laravel [custom cast](https://laravel.com/docs/8.x/eloquent-mutators#custom-casts) that handles serializing between the `object` (or a compatible array) and your JSON database column. It uses [Symfony's Serializer](https://symfony.com/doc/current/components/serializer.html) to do this.

This package is inspired by [Laravel Castable Data Transfer Object](https://github.com/jessarcher/laravel-castable-data-transfer-object)!


## Installation

You can install the package via composer:

```bash
composer require morrislaptop/laravel-popo-caster
```

## Serializer Usage

### 1. Create your `POPO`

``` php
namespace App\Values;

class Address
{
    public function __construct(
        public string $street,
        public string $suburb,
        public string $state,
        public Carbon $moved_at,
    ) {
    }
}
```

### 2. Configure your Eloquent attribute to cast to it:

Note that this should be a `jsonb` or `json` column in your database schema. Objects and arrays are both supported.

```php
namespace App\Models;

use App\Values\Address;
use Illuminate\Database\Eloquent\Model;
use Morrislaptop\LaravelPopoCaster\Serializer;

/**
 * @property Address $address
 */
class User extends Model
{
    protected $casts = [
        'address' => Serializer::class . ':' . Address::class,
        'prev_addresses' => Serializer::class . ':' . Address::class . '[]',
    ];
}
```

And that's it! You can now pass either an instance of your `Address` class, or even just an array with a compatible structure. It will automatically be cast between your class and JSON for storage and the data will be validated on the way in and out.

```php
$user = User::create([
    // ...
    'address' => [
        'street' => '1640 Riverside Drive',
        'suburb' => 'Hill Valley',
        'state' => 'California',
        'moved_at' => now(),
    ],
    'addresses' => [
        [
            'street' => '42 Wallaby Way',
            'suburb' => 'Sydney',
            'state' => 'NSW',
            'moved_at' => '2020-01-14T00:00:00Z',
        ],
    ]
])

$residents = User::where('address->suburb', 'Hill Valley')->get();
```

But the best part is that you can decorate your class with domain-specific methods to turn it into a powerful value object.

```php
$user->address->toMapUrl();

$user->address->getCoordinates();

$user->address->getPostageCost($sender);

$user->address->calculateDistance($otherUser->address);

$user->address->moved_at->diffForHumans();

echo (string) $user->address;
```

## Normalizer Usage

### 1. Create your `POPO`

``` php
namespace App\Values;

class Money
{
    public function __construct(
        public int $amount,
        public string $currency,
    ) {
    }
}
```

### 2. Configure your Eloquent attribute to cast to it:

Note that the properties of your value object should be columns in your database schema. 

```php
namespace App\Models;

use App\Values\Money;
use Illuminate\Database\Eloquent\Model;
use Morrislaptop\LaravelPopoCaster\Normalizer;

/**
 * @property Money $money
 */
class User extends Model
{
    protected $casts = [
        'money' => Normalizer::class . ':' . Money::class,
    ];
}
```

And that's it! You can now pass either an instance of your `Money` class, or set the individual properties on the model. It will automatically be cast between your class and properties for storage and the data will be validated on the way in and out.

```php
$user = User::create([
    // ...
    'amount' => 1000,
    'curency' => 'AUD',
]);

$user = User::create([
    // ...
    'money' => new Money(1000, 'AUD'),
])
```

But the best part is that you can decorate your class with domain-specific methods to turn it into a powerful value object.

```php
$user->money->convertTo('USD');
```

## Plug

Want an easy way to mock or have factories for your POPOs? Check out [morrislaptop/popo-factory](https://github.com/morrislaptop/popo-factory)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Craig Morris](https://github.com/morrislaptop)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
