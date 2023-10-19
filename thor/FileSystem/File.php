<?php

namespace Thor\FileSystem;

use Thor\Stream\Stream;

class File
{

    private bool $syncWithDisk = false;
    private Stream $content;

    /**
     * @param string             $filename
     * @param Stream|string|null $content
     */
    public function __construct(private readonly string $filename, Stream|string|null $content = null)
    {
        $this->content = match (true) {
            $content instanceof Stream => $content,
            is_string($content) => Stream::create($content),
            default => Stream::createFromFile($this->filename, 'rw'),
        };
    }

    /**
     * @param string $path
     *
     * @return static
     */
    public static function readFromDisk(string $path): static
    {
        $fd = new static($path, Stream::createFromFile($path, 'r'));
        $fd->syncWithDisk = true;
        return $fd;
    }

    /**
     * Gets the raw stream of the file. De-synchronize the file with the disk until the next call to writeToDisk().
     *
     * @return Stream
     * @see self::writeToDisk()
     *
     */
    public function stream(): Stream
    {
        $this->syncWithDisk = false;
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isSynced(): bool
    {
        return $this->syncWithDisk;
    }

    /**
     * @return void
     */
    public function writeToDisk(): void
    {
        $this->syncWithDisk = true;
        Stream::copyToStream(
            $this->content,
            Stream::createFromFile($this->filename, 'w+')
        );
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return $this->content->getSize() ?? 0;
    }

    /**
     * @param string $data
     *
     * @return void
     */
    public function append(string $data): void
    {
        $this->syncWithDisk = false;
        $this->content->write($data);
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function read(int $length = 1): string
    {
        return $this->content->read($length);
    }

    /**
     * @return string
     */
    public function readAll(): string
    {
        return $this->content->getContents();
    }

}
