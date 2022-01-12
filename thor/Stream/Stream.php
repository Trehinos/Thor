<?php

namespace Thor\Stream;

use RuntimeException;
use InvalidArgumentException;

/**
 * Provides an object-oriented style for stream operations.
 *
 * @package          Thor/Stream
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class Stream implements StreamInterface
{

    private const READABLE_MODES = '/r|a\+|ab\+|w\+|wb\+|x\+|xb\+|c\+|cb\+/';
    private const WRITABLE_MODES = '/a|w|r\+|rb\+|rw|x|c/';

    /**
     * @param resource
     * @param array $options
     *
     * @throws InvalidArgumentException
     */
    public function __construct(private $stream, private array $options = [])
    {
        if (!is_resource($this->stream)) {
            throw new InvalidArgumentException('Stream must be a resource.');
        }
    }

    /**
     * Copy the content of the Stream into the returned string.
     *
     * @param StreamInterface $stream
     * @param int             $maxLen
     *
     * @return string
     */
    public static function copyToString(StreamInterface $stream, int $maxLen = -1): string
    {
        $buffer = '';
        $len = 0;
        while (!$stream->eof() && ($maxLen === -1 || $len < $maxLen)) {
            $read = $stream->read($maxLen === -1 ? PHP_INT_MAX : $maxLen - $len);
            $len = strlen($read);
            $buffer .= $read;
        }
        return $buffer;
    }

    /**
     * @inheritDoc
     */
    public function eof(): bool
    {
        if (null === $this->stream) {
            throw new RuntimeException('Stream is detached');
        }

        return feof($this->stream);
    }

    /**
     * @inheritDoc
     */
    public function read(int $length): string
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->isReadable()) {
            throw new RuntimeException('Cannot read from non-readable stream');
        }
        if ($length < 0) {
            throw new RuntimeException('Length parameter cannot be negative');
        }
        if (0 === $length) {
            return '';
        }
        if (false === ($read = fread($this->stream, $length))) {
            throw new RuntimeException('Unable to read from stream');
        }
        return $read;
    }

    /**
     * @inheritDoc
     */
    public function isReadable(): bool
    {
        return preg_match(self::READABLE_MODES, $this->getMetadata('mode')) === 1;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata(?string $key = null): mixed
    {
        if (null === $this->stream) {
            return $key ? null : [];
        }
        $meta = $this->options + stream_get_meta_data($this->stream);
        if (!$key) {
            return $meta;
        }
        return $meta[$key] ?? null;
    }

    /**
     * Copy a Stream into another Stream.
     *
     * @param StreamInterface $source
     * @param StreamInterface $dest
     * @param int             $maxLen
     * @param int             $bufferSize
     *
     * @return void
     */
    public static function copyToStream(
        StreamInterface $source,
        StreamInterface $dest,
        int $maxLen = -1,
        int $bufferSize = 8192
    ): void {
        if ($maxLen === -1) {
            while (!$source->eof()) {
                if (!$dest->write($source->read($bufferSize))) {
                    break;
                }
            }
        } else {
            $remaining = $maxLen;
            while ($remaining > 0 && !$source->eof()) {
                $buf = $source->read(min($bufferSize, $remaining));
                $len = strlen($buf);
                if (!$len) {
                    break;
                }
                $remaining -= $len;
                $dest->write($buf);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function write(string $string): int
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->isWritable()) {
            throw new RuntimeException('Cannot write to a non-writable stream');
        }
        if (false === ($result = fwrite($this->stream, $string))) {
            throw new RuntimeException('Unable to write to stream');
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function isWritable(): bool
    {
        return preg_match(self::WRITABLE_MODES, $this->getMetadata('mode')) === 1;
    }

    /**
     * Creates a new Stream object from resource.
     *
     * @param resource $resource
     *
     * @return static
     */
    public static function createFromResource($resource): self
    {
        return new self($resource);
    }

    /**
     * Creates a new readable Stream with specified data.
     *
     * @param string $data
     *
     * @return static
     */
    public static function create(string $data): self
    {
        $file = self::createFromFile('php://temp', 'r+');
        $file->write($data);
        $file->rewind();
        return $file;
    }

    /**
     * Opens a file and returns the corresponding Stream.
     *
     * @param string $filename
     * @param string $mode
     *
     * @return static
     */
    public static function createFromFile(string $filename, string $mode): self
    {
        return new self(self::openFile($filename, $mode));
    }

    /**
     * Open a file and returns the resource stream.
     *
     * This is a utility function.
     *
     * @param string $filename
     * @param string $mode
     *
     * @return resource
     *
     * @throws RuntimeException
     */
    public static function openFile(string $filename, string $mode)
    {
        $ex = null;
        set_error_handler(static function (int $errno, string $errstr) use ($filename, $mode, &$ex): bool {
            $ex = new RuntimeException(
                sprintf(
                    'Unable to open "%s" using mode "%s": %s',
                    $filename,
                    $mode,
                    $errstr
                )
            );

            return true;
        });
        try {
            /** @var resource $handle */
            $handle = fopen($filename, $mode);
        } catch (\Throwable $e) {
            $ex = new RuntimeException(
                sprintf(
                    'Unable to open "%s" using mode "%s": %s',
                    $filename,
                    $mode,
                    $e->getMessage()
                ), 0, $e
            );
        }
        restore_error_handler();
        if ($ex) {
            throw $ex;
        }
        return $handle;
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * @inheritDoc
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (null === $this->stream) {
            throw new RuntimeException('Stream is detached');
        }
        if (!$this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }
        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException("Unable to seek to stream position $offset with whence $whence...");
        }
    }

    /**
     * @inheritDoc
     */
    public function isSeekable(): bool
    {
        return $this->getMetadata('seekable') ?? false;
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        if (null !== $this->stream) {
            fclose($this->stream);
            $this->detach();
        }
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        if (null === $this->stream) {
            return null;
        }
        $result = $this->stream;
        $this->stream = null;
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        if ($this->isSeekable()) {
            $this->seek(0);
        }
        return $this->getContents();
    }

    /**
     * @inheritDoc
     */
    public function getContents(): string
    {
        if (null === $this->stream) {
            throw new RuntimeException('Stream is detached');
        }
        return stream_get_contents($this->stream) ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        if (null === $this->stream) {
            return null;
        }
        $stats = fstat($this->stream);
        return $stats['size'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function tell(): int
    {
        if (null === $this->stream) {
            throw new RuntimeException('Stream is detached');
        }
        return ftell($this->stream) ?: 0;
    }
}
