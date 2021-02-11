<?php

namespace Morrislaptop\LaravelPopoCaster;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Morrislaptop\LaravelPopoCaster\Normalizer\CarbonNormalizer;
use Morrislaptop\LaravelPopoCaster\Normalizer\ObjectNormalizer;
use Morrislaptop\LaravelPopoCaster\Normalizer\ModelIdentifierNormalizer;

class CasterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(Serializer::class, function () {
            return new Serializer([
                new CarbonNormalizer,
                new ModelIdentifierNormalizer,
                new \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer,
                new \Symfony\Component\Serializer\Normalizer\ArrayDenormalizer,
                new ObjectNormalizer,
            ], [new JsonEncoder()]);
        });
    }
}
