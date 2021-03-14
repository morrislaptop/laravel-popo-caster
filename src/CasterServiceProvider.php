<?php

namespace Morrislaptop\LaravelPopoCaster;

use Illuminate\Support\ServiceProvider;
use Morrislaptop\SymfonyCustomNormalizers\CarbonNormalizer;
use Morrislaptop\SymfonyCustomNormalizers\ModelIdentifierNormalizer;
use Morrislaptop\SymfonyCustomNormalizers\ObjectWithDocblocksNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

class CasterServiceProvider extends ServiceProvider
{
    /**
     * Set up the Symfony serializer with some recommended normalizers
     * for Laravel projects. Feel free to override the Serializer
     * definition with suitable normalizers for your project.
     */
    public function register()
    {
        $this->app->bind(Serializer::class, function () {
            return new Serializer([
                new CarbonNormalizer,
                new ModelIdentifierNormalizer,
                new DateTimeNormalizer,
                new ArrayDenormalizer,
                new ObjectWithDocblocksNormalizer,
            ], [new JsonEncoder()]);
        });
    }
}
