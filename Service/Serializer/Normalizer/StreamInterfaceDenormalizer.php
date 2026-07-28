<?php

/*
 * This file is part of the auto1-oss/service-api-components-bundle.
 *
 * (c) AUTO1 Group SE https://www.auto1-group.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Auto1\ServiceAPIComponentsBundle\Service\Serializer\Normalizer;

use Psr\Http\Message\StreamInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class StreamInterfaceDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, ?string $format = null, array $context = []): ?StreamInterface
    {
        if (null === $data) {
            return null;
        }

        if (!$data instanceof StreamInterface) {
            throw NotNormalizableValueException::createForUnexpectedDataType(
                sprintf('Expected an instance of "%s".', StreamInterface::class),
                $data,
                [StreamInterface::class],
                $context['deserialization_path'] ?? null
            );
        }

        return $data;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_a($type, StreamInterface::class, true);
    }

    /**
     * Required by DenormalizerInterface since symfony/serializer 7.0. `'object' => false`
     * because supportsDenormalization() matches subclasses via is_a(), and false keeps it
     * in the loop rather than caching a wrong answer for a concrete implementation.
     */
    public function getSupportedTypes(?string $format): array
    {
        return ['object' => false];
    }
}
