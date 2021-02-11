<?php

namespace Morrislaptop\LaravelPopoCaster;

use Illuminate\Support\ServiceProvider;
use Morrislaptop\LaravelPopoCaster\Normalizer\CarbonNormalizer;
use Morrislaptop\LaravelPopoCaster\Normalizer\ModelIdentifierNormalizer;
use Morrislaptop\LaravelPopoCaster\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

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
