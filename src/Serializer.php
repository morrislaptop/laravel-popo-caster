<?php

namespace Morrislaptop\LaravelPopoCaster;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class Serializer implements CastsAttributes
{
    protected string $class;

    protected SymfonySerializer $serializer;

    /**
     * @param string $class The DataTransferObject class to cast to
     */
    public function __construct(string $class)
    {
        $this->class = $class;
        $this->serializer = resolve(SymfonySerializer::class);
    }

    /**
     * Cast the stored value to the configured DataTransferObject.
     */
    public function get($model, $key, $value, $attributes) : mixed
    {
        if (is_null($value)) {
            return null;
        }

        return $this->serializer->deserialize($value, $this->class, 'json');
    }

    /**
     * Prepare the given value for storage.
     */
    public function set($model, $key, $value, $attributes) : ?string
    {
        if (is_null($value)) {
            return null;
        }

        $isCollection = Str::endsWith($this->class, '[]');

        $value = $isCollection
            ? array_map(fn ($value) => $this->resolveInstance($value), $value)
            : $this->resolveInstance($value);

        return $this->serializer->serialize($value, 'json');
    }

    protected function resolveInstance($value)
    {
        $class = Str::replaceLast('[]', '', $this->class);

        if (is_array($value)) {
            $value = $this->serializer->denormalize($value, $class);
        }

        if (! $value instanceof $class) {
            throw new InvalidArgumentException("Value must be of type [$class], array, or null");
        }

        return $value;
    }
}
