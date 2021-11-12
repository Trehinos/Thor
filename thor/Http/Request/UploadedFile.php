<?php

namespace Thor\Http\Request;

use RuntimeException;
use Thor\Stream\Stream;
use InvalidArgumentException;
use Thor\Stream\StreamInterface;

class UploadedFile implements UploadedFileInterface
{

    private bool $moved = false;

    /**
     * @param StreamInterface $stream
     * @param int|null        $size
     * @param UploadError     $errorStatus
     * @param string|null     $clientFilename
     * @param string|null     $clientMediaType
     */
    public function __construct(
        private StreamInterface $stream,
        private ?int $size,
        private UploadError $errorStatus,
        private ?string $clientFilename = null,
        private ?string $clientMediaType = null
    ) {
    }

    public static function normalizeFiles(array $files): array
    {
        $normalized = [];

        foreach ($files as $key => $value) {
            if ($value instanceof UploadedFileInterface) {
                $normalized[$key] = $value;
            } elseif (is_array($value) && isset($value['tmp_name'])) {
                $normalized[$key] = self::createUploadedFileFromSpec($value);
            } elseif (is_array($value)) {
                $normalized[$key] = self::normalizeFiles($value);
            } else {
                throw new InvalidArgumentException('Invalid value in files specification');
            }
        }

        return $normalized;
    }

    private static function createUploadedFileFromSpec(array $value): UploadedFile|array
    {
        if (is_array($value['tmp_name'])) {
            return self::normalizeNestedFileSpec($value);
        }

        return new self(
            $value['tmp_name'],
            (int)$value['size'],
            UploadError::from((int)$value['error']),
            $value['name'],
            $value['type']
        );
    }

    private static function normalizeNestedFileSpec(array $files = []): array
    {
        $normalizedFiles = [];

        foreach (array_keys($files['tmp_name']) as $key) {
            $spec = [
                'tmp_name' => $files['tmp_name'][$key],
                'size'     => $files['size'][$key],
                'error'    => $files['error'][$key],
                'name'     => $files['name'][$key],
                'type'     => $files['type'][$key],
            ];
            $normalizedFiles[$key] = self::createUploadedFileFromSpec($spec);
        }

        return $normalizedFiles;
    }

    public function getStream(): StreamInterface
    {
        return $this->stream;
    }

    private function validateActive(): void
    {
        if ($this->errorStatus !== UploadError::NO_ERROR) {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }
        if ($this->moved) {
            throw new RuntimeException('Cannot retrieve stream after it has already been moved');
        }
    }

    public function moveTo(string $targetPath): void
    {
        $this->validateActive();
        if ($targetPath === '') {
            throw new InvalidArgumentException('Invalid path provided for move operation; must be a non-empty string');
        }
        Stream::copyToStream($this->stream, Stream::createFromFile($targetPath, 'w'));
        $this->moved = true;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getError(): UploadError
    {
        return $this->errorStatus;
    }

    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}