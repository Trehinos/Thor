<?php

namespace Thor\Http\Request;

use RuntimeException;
use InvalidArgumentException;
use Thor\FileSystem\Stream\Stream;
use Thor\FileSystem\Stream\StreamInterface;

/**
 * This class can be used to manage files uploaded by an HTML form.
 *
 * @package          Thor/Http/Request
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
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

    /**
     * Translate the original $_FILES[field][name] = [...]
     * to an $array[name] = UploadedFile(...).
     *
     * @param array $files
     *
     * @return array
     */
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

    /**
     * @param array $value
     *
     * @return UploadedFile|array
     */
    private static function createUploadedFileFromSpec(array $value): UploadedFile|array
    {
        if (is_array($value['tmp_name'])) {
            if ($value['tmp_name'][0] === '') {
                return [];
            }
            return self::normalizeNestedFileSpec($value);
        }
        return new self(
            Stream::createFromFile($value['tmp_name'], 'r'),
            (int)$value['size'],
            UploadError::from((int)$value['error']),
            $value['name'],
            $value['type']
        );
    }

    /**
     * @param array $files
     *
     * @return array
     */
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

    /**
     * @inheritDoc
     */
    public function getStream(): StreamInterface
    {
        return $this->stream;
    }

    /**
     * @return void
     */
    private function validateActive(): void
    {
        if ($this->errorStatus !== UploadError::NO_ERROR) {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }
        if ($this->moved) {
            throw new RuntimeException('Cannot retrieve stream after it has already been moved');
        }
    }

    /**
     * @inheritDoc
     */
    public function moveTo(string $targetPath): void
    {
        $this->validateActive();
        if ($targetPath === '') {
            throw new InvalidArgumentException('Invalid path provided for move operation; must be a non-empty string');
        }
        Stream::copyToStream($this->stream, Stream::createFromFile($targetPath, 'w'));
        $this->moved = true;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function getError(): UploadError
    {
        return $this->errorStatus;
    }

    /**
     * @inheritDoc
     */
    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    /**
     * @inheritDoc
     */
    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}
