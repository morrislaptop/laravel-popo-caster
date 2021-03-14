<?php

namespace Morrislaptop\LaravelPopoCaster;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionParameter;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class Normalizer implements CastsAttributes, SerializesCastableAttributes
{
    /**
     * @var class-string
     */
    protected string $class;

    protected SymfonySerializer $serializer;

    /**
     * @param class-string $class The DataTransferObject class to cast to
     */
    public function __construct(string $class)
    {
        $this->class = $class;
        $this->serializer = resolve(SymfonySerializer::class);
    }

    /**
     * Cast the stored value to the configured DataTransferObject.
     */
    public function get($model, $key, $value, $attributes)
    {
        // $attributes contains the entire model, let's see what props
        // $this->class has and only get that data. If all the data
        // is null then we should probably just return null too.
        $props = $this->getClassProps();

        $data = Arr::only($attributes, $props);

        if (empty(array_filter($data, fn ($a) => $a !== null))) {
            return;
        }

        return $this->serializer->denormalize($data, $this->class);
    }

    /**
     * Prepare the given value for storage.
     */
    public function set($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return;
        }

        if (is_array($value)) {
            return $value;
        }

        return $this->serializer->normalize($value);
    }

    public function serialize($model, string $key, $value, array $attributes)
    {
        return $this->set($model, $key, $value, $attributes);
    }

    protected function getClassProps(): array
    {
        $reflect = new ReflectionClass($this->class);
        $params = optional($reflect->getConstructor())->getParameters() ?? [];

        return array_map(fn (ReflectionParameter $param): string => $param->getName(), $params);
    }
}
