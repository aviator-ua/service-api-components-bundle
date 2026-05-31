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

namespace Tests\Auto1\ServiceAPIComponentsBundle\Serializer\Normalizer;

use Auto1\ServiceAPIComponentsBundle\Serializer\Normalizer\StreamInterfaceDenormalizer;
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
        $cut = $this->getCut();

        $result = $cut->supportsDenormalization(null, StreamInterface::class);

        $this->assertTrue($result);
    }

    public function testDoesNotSupportOtherTypes(): void
    {
        $targetUnsupportedType = \stdClass::class;

        $cut = $this->getCut();

        $result = $cut->supportsDenormalization(null, $targetUnsupportedType);

        $this->assertFalse($result);
    }

    public function testPassesThroughStreamInstance(): void
    {
        $targetStream = $this->createMock(StreamInterface::class);

        $cut = $this->getCut();

        $result = $cut->denormalize($targetStream, StreamInterface::class);

        $this->assertSame($targetStream, $result);
    }

    public function testReturnsNullForNullData(): void
    {
        $cut = $this->getCut();

        $result = $cut->denormalize(null, StreamInterface::class);

        $this->assertNull($result);
    }

    public function testThrowsForNonStreamData(): void
    {
        $targetNonStreamData = 'not a stream';

        $cut = $this->getCut();

        $this->expectException(\InvalidArgumentException::class);
        $cut->denormalize($targetNonStreamData, StreamInterface::class);
    }
}
