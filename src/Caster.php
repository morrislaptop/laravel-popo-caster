<?php

namespace Morrislaptop\Caster;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Serializer\Serializer;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Caster implements CastsAttributes
{
    protected string $class;

    protected Serializer $serializer;

    /**
     * @param string $class The DataTransferObject class to cast to
     */
    public function __construct(string $class)
    {
        $this->class = $class;
        $this->serializer = resolve(Serializer::class);
    }

    /**
     * Cast the stored value to the configured DataTransferObject.
     */
    public function get($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return;
        }

        return $this->serializer->deserialize($value, $this->class, 'json');
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
            $value = $this->serializer->denormalize($value, $this->class);
        }

        $instance = is_array($value) ? reset($value) : $value;
        $class = Str::replaceLast('[]', '', $this->class);

        if (! $instance instanceof $class) {
            throw new InvalidArgumentException("Value must be of type [$this->class], array, or null");
        }

        return $this->serializer->serialize($value, 'json');
    }
}
