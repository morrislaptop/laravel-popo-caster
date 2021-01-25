<?php

namespace Morrislaptop\Caster;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class CasterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(Serializer::class, function () {
            return new Serializer([
                new \Spatie\EventSourcing\Support\CarbonNormalizer,
                new \Spatie\EventSourcing\Support\ModelIdentifierNormalizer,
                new \Symfony\Component\Serializer\Normalizer\DateTimeNormalizer,
                new \Symfony\Component\Serializer\Normalizer\ArrayDenormalizer,
                new \Spatie\EventSourcing\Support\ObjectNormalizer,
            ], [new JsonEncoder()]);
        });
    }
}
