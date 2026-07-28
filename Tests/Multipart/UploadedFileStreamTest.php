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
    private const TARGET_MIME_TYPE = 'image/png';
    private const TARGET_FILENAME = 'image.png';
    private const TARGET_MODE = 'r';
    private const TARGET_PAYLOAD = 'Test Payload';

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
            ->willReturn(self::TARGET_MIME_TYPE)
        ;

        $this->inner
            ->expects(self::never())
            ->method('getMetadata')
        ;

        $service = $this->getCut();
        $result = $service->getMetadata('mime-type');

        self::assertSame(self::TARGET_MIME_TYPE, $result);
    }

    public function testGetMetadataFilenameReturnsClientOriginalName(): void
    {
        $this->uploadedFile
            ->method('getClientOriginalName')
            ->willReturn(self::TARGET_FILENAME)
        ;

        $this->inner
            ->expects(self::never())
            ->method('getMetadata')
        ;

        $service = $this->getCut();
        $result = $service->getMetadata('filename');

        self::assertSame(self::TARGET_FILENAME, $result);
    }

    public function testGetMetadataWithoutKeyMergesInnerWithMimeTypeAndFilename(): void
    {
        $fileMetadata = [
            'wrapper_type' => 'plainfile',
            'mode' => self::TARGET_MODE,
        ];

        $this->inner
            ->method('getMetadata')
            ->with(null)
            ->willReturn($fileMetadata)
        ;

        $this->uploadedFile
            ->method('getClientMimeType')
            ->willReturn(self::TARGET_MIME_TYPE)
        ;

        $this->uploadedFile
            ->method('getClientOriginalName')
            ->willReturn(self::TARGET_FILENAME)
        ;

        $service = $this->getCut();
        $result = $service->getMetadata();

        self::assertSame(
            array_merge(
                $fileMetadata,
                [
                    'mime-type' => self::TARGET_MIME_TYPE,
                    'filename' => self::TARGET_FILENAME,
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
            ->willReturn(self::TARGET_MODE)
        ;

        $service = $this->getCut();
        $result = $service->getMetadata('mode');

        self::assertSame(self::TARGET_MODE, $result);
    }

    public function testReadDelegatesToInner(): void
    {
        $targetLength = 1024;

        $this->inner->expects(self::once())
            ->method('read')
            ->with($targetLength)
            ->willReturn(self::TARGET_PAYLOAD)
        ;

        $service = $this->getCut();
        $result = $service->read($targetLength);

        self::assertSame(self::TARGET_PAYLOAD, $result);
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
            ->willReturn(self::TARGET_PAYLOAD)
        ;

        $service = $this->getCut();
        $result = $service->getContents();

        self::assertSame(self::TARGET_PAYLOAD, $result);
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
            ->with(self::TARGET_PAYLOAD)
            ->willReturn($targetBytesWritten)
        ;

        $service = $this->getCut();
        $result = $service->write(self::TARGET_PAYLOAD);

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

    public function testGetUploadedFileReturnsConstructorArgument(): void
    {
        $service = $this->getCut();
        $result = $service->getUploadedFile();

        self::assertSame($this->uploadedFile, $result);
    }

    private function getCut(): UploadedFileStream
    {
        return new UploadedFileStream($this->inner, $this->uploadedFile);
    }
}
