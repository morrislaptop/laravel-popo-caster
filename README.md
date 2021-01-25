# Automatically cast JSON columns to rich PHP objects in Laravel using Symfony's Serializer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/morrislaptop/laravel-castable-object.svg?style=flat-square)](https://packagist.org/packages/morrislaptop/laravel-castable-object)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/morrislaptop/laravel-castable-object/run-tests?label=tests)](https://github.com/morrislaptop/laravel-castable-object/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/morrislaptop/laravel-castable-object.svg?style=flat-square)](https://packagist.org/packages/morrislaptop/laravel-castable-object)


This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require morrislaptop/laravel-castable-object
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Morrislaptop\Caster\CasterServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Morrislaptop\Caster\CasterServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$laravel-castable-object = new Morrislaptop\Caster();
echo $laravel-castable-object->echoPhrase('Hello, Morrislaptop!');
```

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
