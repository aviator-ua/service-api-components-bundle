<?php

/*
 * This file is part of the auto1-oss/service-api-handler-bundle.
 *
 * (c) AUTO1 Group SE https://www.auto1-group.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Auto1\ServiceAPIComponentsBundle\Service\Serializer\Normalizer;

use Psr\Http\Message\StreamInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class StreamInterfaceDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, ?string $format = null, array $context = []): ?StreamInterface
    {
        if (null === $data) {
            return null;
        }

        if (!$data instanceof StreamInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Expected an instance of "%s", got "%s".',
                StreamInterface::class,
                get_debug_type($data)
            ));
        }

        return $data;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_a($type, StreamInterface::class, true);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            StreamInterface::class => true,
        ];
    }
}
