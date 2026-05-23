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

namespace Auto1\ServiceAPIComponentsBundle\Tests\Multipart;

use Auto1\ServiceAPIComponentsBundle\Multipart\UploadedFileStream;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedFileStreamTest extends TestCase
{
    private static $targetMimeType = 'image/png';
    private static $targetFilename = 'file.txt';
    private static $targetMode = 'r';
    private static $targetPayload = 'Test Payload';

    /**
     * @var StreamInterface&MockObject
     */
    private $inner;

    /**
     * @var UploadedFile&MockObject
     */
    private $uploadedFile;

    protected function setUp(): void
    {
        $this->inner = $this->createMock(StreamInterface::class);
        $this->uploadedFile = $this->createMock(UploadedFile::class);
    }

    public function testGetMetadataMimeTypeReturnsClientMimeType(): void
    {
        $this->uploadedFile
            ->method('getClientMimeType')
            ->willReturn(self::$targetMimeType);
        ;

        $this->inner
            ->expects(self::never())
            ->method('getMetadata')
        ;

        $service = $this->getCut();
        $result = $service->getMetadata('mime-type');

        self::assertSame(self::$targetMimeType, $result);
    }

    public function testGetMetadataFilenameReturnsClientOriginalName(): void
    {
        $this->uploadedFile
            ->method('getClientOriginalName')
            ->willReturn(self::$targetFilename)
        ;

        $this->inner
            ->expects(self::never())
            ->method('getMetadata')
        ;

        $service = $this->getCut();
        $result = $service->getMetadata('filename');

        self::assertSame(self::$targetFilename, $result);
    }

    public function testGetMetadataWithoutKeyMergesInnerWithMimeTypeAndFilename(): void
    {
        $fileMetadata = [
            'wrapper_type' => 'plainfile',
            'mode' => self::$targetMode,
        ];

        $this->inner
            ->method('getMetadata')
            ->with(null)
            ->willReturn($fileMetadata)
        ;

        $this->uploadedFile
            ->method('getClientMimeType')
            ->willReturn(self::$targetMimeType)
        ;

        $this->uploadedFile
            ->method('getClientOriginalName')
            ->willReturn(self::$targetFilename)
        ;

        $service = $this->getCut();
        $result = $service->getMetadata();

        self::assertSame(
            array_merge(
                $fileMetadata,
                [
                    'mime-type' => self::$targetMimeType,
                    'filename' => self::$targetFilename,
                ]
            ),
            $result
        );
    }

    public function testGetMetadataOtherKeyDelegatesToInner(): void
    {
        $this->inner
            ->expects(self::once())
            ->method('getMetadata')
            ->with('mode')
            ->willReturn(self::$targetMode)
        ;

        $service = $this->getCut();
        $result = $service->getMetadata('mode');

        self::assertSame(self::$targetMode, $result);
    }

    public function testReadDelegatesToInner(): void
    {
        $targetLength = 1024;

        $this->inner->expects(self::once())
            ->method('read')
            ->with($targetLength)
            ->willReturn(self::$targetPayload)
        ;

        $service = $this->getCut();
        $result = $service->read($targetLength);

        self::assertSame(self::$targetPayload, $result);
    }

    public function testSeekDelegatesToInner(): void
    {
        $targetOffset = 100;

        $this->inner
            ->expects(self::once())
            ->method('seek')
            ->with($targetOffset, SEEK_SET)
        ;

        $service = $this->getCut();
        $service->seek($targetOffset);
    }

    public function testGetContentsDelegatesToInner(): void
    {
        $this->inner
            ->expects(self::once())
            ->method('getContents')
            ->willReturn(self::$targetPayload)
        ;

        $service = $this->getCut();
        $result = $service->getContents();

        self::assertSame(self::$targetPayload, $result);
    }

    public function testToStringDelegatesToInner(): void
    {
        $stringified = 'targetStringified';

        $this->inner
            ->expects(self::once())
            ->method('__toString')
            ->willReturn($stringified)
        ;

        $service = $this->getCut();

        self::assertSame($stringified, (string) $service);
    }

    public function testCloseDelegatesToInner(): void
    {
        $this->inner
            ->expects(self::once())
            ->method('close')
        ;

        $service = $this->getCut();
        $service->close();
    }

    public function testDetachDelegatesToInner(): void
    {
        $targetResource = fopen('php://memory', 'r');

        $this->inner
            ->expects(self::once())
            ->method('detach')
            ->willReturn($targetResource)
        ;

        $service = $this->getCut();
        $result = $service->detach();

        self::assertSame($targetResource, $result);

        fclose($targetResource);
    }

    public function testGetSizeDelegatesToInner(): void
    {
        $targetSize = 2048;

        $this->inner
            ->expects(self::once())
            ->method('getSize')
            ->willReturn($targetSize)
        ;

        $service = $this->getCut();
        $result = $service->getSize();

        self::assertSame($targetSize, $result);
    }

    public function testTellDelegatesToInner(): void
    {
        $targetPosition = 42;

        $this->inner
            ->expects(self::once())
            ->method('tell')
            ->willReturn($targetPosition)
        ;

        $service = $this->getCut();
        $result = $service->tell();

        self::assertSame($targetPosition, $result);
    }

    public function testEofDelegatesToInner(): void
    {
        $targetEof = true;

        $this->inner
            ->expects(self::once())
            ->method('eof')
            ->willReturn($targetEof)
        ;

        $service = $this->getCut();
        $result = $service->eof();

        self::assertSame($targetEof, $result);
    }

    public function testIsSeekableDelegatesToInner(): void
    {
        $targetIsSeekable = true;

        $this->inner
            ->expects(self::once())
            ->method('isSeekable')
            ->willReturn($targetIsSeekable)
        ;

        $service = $this->getCut();
        $result = $service->isSeekable();

        self::assertSame($targetIsSeekable, $result);
    }

    public function testRewindDelegatesToInner(): void
    {
        $this->inner
            ->expects(self::once())
            ->method('rewind')
        ;

        $service = $this->getCut();
        $service->rewind();
    }

    public function testIsWritableDelegatesToInner(): void
    {
        $targetIsWritable = false;

        $this->inner
            ->expects(self::once())
            ->method('isWritable')
            ->willReturn($targetIsWritable)
        ;

        $service = $this->getCut();
        $result = $service->isWritable();

        self::assertSame($targetIsWritable, $result);
    }

    public function testWriteDelegatesToInner(): void
    {
        $targetBytesWritten = 12;

        $this->inner
            ->expects(self::once())
            ->method('write')
            ->with(self::$targetPayload)
            ->willReturn($targetBytesWritten)
        ;

        $service = $this->getCut();
        $result = $service->write(self::$targetPayload);

        self::assertSame($targetBytesWritten, $result);
    }

    public function testIsReadableDelegatesToInner(): void
    {
        $targetIsReadable = true;

        $this->inner
            ->expects(self::once())
            ->method('isReadable')
            ->willReturn($targetIsReadable)
        ;

        $service = $this->getCut();
        $result = $service->isReadable();

        self::assertSame($targetIsReadable, $result);
    }

    private function getCut(): UploadedFileStream
    {
        return new UploadedFileStream($this->inner, $this->uploadedFile);
    }
}
