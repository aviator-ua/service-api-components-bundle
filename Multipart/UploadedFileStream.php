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

namespace Auto1\ServiceAPIComponentsBundle\Multipart;

use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadedFileStream implements StreamInterface
{
    private StreamInterface $inner;

    private UploadedFile $uploadedFile;

    public function __construct(StreamInterface $inner, UploadedFile $uploadedFile)
    {
        $this->inner = $inner;
        $this->uploadedFile = $uploadedFile;
    }

    public function __toString(): string
    {
        return (string) $this->inner;
    }

    public function close(): void
    {
        $this->inner->close();
    }

    public function detach()
    {
        return $this->inner->detach();
    }

    public function getSize(): ?int
    {
        return $this->inner->getSize();
    }

    public function tell(): int
    {
        return $this->inner->tell();
    }

    public function eof(): bool
    {
        return $this->inner->eof();
    }

    public function isSeekable(): bool
    {
        return $this->inner->isSeekable();
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        $this->inner->seek($offset, $whence);
    }

    public function rewind(): void
    {
        $this->inner->rewind();
    }

    public function isWritable(): bool
    {
        return $this->inner->isWritable();
    }

    public function write(string $string): int
    {
        return $this->inner->write($string);
    }

    public function isReadable(): bool
    {
        return $this->inner->isReadable();
    }

    public function read(int $length): string
    {
        return $this->inner->read($length);
    }

    public function getContents(): string
    {
        return $this->inner->getContents();
    }

    public function getMetadata(?string $key = null)
    {
        if ('mime-type' === $key) {
            return $this->uploadedFile->getClientMimeType();
        }

        if ('filename' === $key) {
            return $this->uploadedFile->getClientOriginalName();
        }

        if (null === $key) {
            $innerMetadata = $this->inner->getMetadata();

            return array_merge(
                is_array($innerMetadata) ? $innerMetadata : [],
                [
                    'mime-type' => $this->uploadedFile->getClientMimeType(),
                    'filename' => $this->uploadedFile->getClientOriginalName(),
                ]
            );
        }

        return $this->inner->getMetadata($key);
    }
}