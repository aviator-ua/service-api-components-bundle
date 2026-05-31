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

namespace Tests\Auto1\ServiceAPIComponentsBundle\Service\Serializer\Normalizer;

use Auto1\ServiceAPIComponentsBundle\Service\Serializer\Normalizer\StreamInterfaceDenormalizer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class StreamInterfaceDenormalizerTest extends TestCase
{
    private function getCut(): StreamInterfaceDenormalizer
    {
        return new StreamInterfaceDenormalizer();
    }

    public function testSupportsDenormalizationForStreamInterface(): void
    {
        $denormalizer = $this->getCut();

        $result = $denormalizer->supportsDenormalization(null, StreamInterface::class);

        self::assertTrue($result);
    }

    public function testDoesNotSupportOtherTypes(): void
    {
        $targetUnsupportedType = \stdClass::class;

        $denormalizer = $this->getCut();

        $result = $denormalizer->supportsDenormalization(null, $targetUnsupportedType);

        self::assertFalse($result);
    }

    public function testPassesThroughStreamInstance(): void
    {
        $targetStream = $this->createMock(StreamInterface::class);

        $denormalizer = $this->getCut();

        $result = $denormalizer->denormalize($targetStream, StreamInterface::class);

        self::assertSame($targetStream, $result);
    }

    public function testReturnsNullForNullData(): void
    {
        $denormalizer = $this->getCut();

        $result = $denormalizer->denormalize(null, StreamInterface::class);

        self::assertNull($result);
    }

    public function testThrowsForNonStreamData(): void
    {
        $targetNonStreamData = 'not a stream';

        $denormalizer = $this->getCut();

        $this->expectException(InvalidArgumentException::class);
        $denormalizer->denormalize($targetNonStreamData, StreamInterface::class);
    }
}
