<?php

namespace Morrislaptop\LaravelPopoCaster\Normalizer;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CarbonNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if (! $object instanceof CarbonInterface) {
            throw new InvalidArgumentException('Cannot serialize an object that is not a CarbonInterface in CarbonNormalizer.');
        }

        return $object->toRfc3339String();
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof CarbonInterface;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return new Carbon($data);
    }

    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return is_a($type, CarbonInterface::class, true);
    }
}
