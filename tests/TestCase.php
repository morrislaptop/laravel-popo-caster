<?php

namespace Morrislaptop\LaravelPopoCaster\Tests;

use Morrislaptop\LaravelPopoCaster\CasterServiceProvider;
use Morrislaptop\SymfonyCustomNormalizers\Brick\MoneyNormalizer;
use Morrislaptop\SymfonyCustomNormalizers\CarbonNormalizer;
use Morrislaptop\SymfonyCustomNormalizers\ModelIdentifierNormalizer;
use Morrislaptop\SymfonyCustomNormalizers\ObjectWithDocblocksNormalizer;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            CasterServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Add some extra normalizers for our tests..
        $app->bind(Serializer::class, function () {
            return new Serializer([
                new MoneyNormalizer, // for NormalizerThirdPartyTest
                new CarbonNormalizer,
                new ModelIdentifierNormalizer,
                new DateTimeNormalizer,
                new ArrayDenormalizer,
                new ObjectWithDocblocksNormalizer,
            ], [new JsonEncoder()]);
        });
    }
}
