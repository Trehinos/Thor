<?php

namespace Thor\FileSystem;

use Thor\Stream\Stream;

// TODO (?)
class File
{

    private bool $syncWithDisk = false;
    private Stream $content;

    public function __construct(private string $filename, Stream|string|null $content = null)
    {
        $this->content = match (true) {
            get_class($content) === Stream::class => $content,
            default => Stream::create($content ?? '')
        };
    }

    public static function readFromDisk(string $path): static
    {
        $fd = new static($path, Stream::createFromFile($path, 'r'));
        $fd->syncWithDisk = true;
        return $fd;
    }

    public function isSynced(): bool
    {
        return $this->syncWithDisk;
    }

    public function writeToDisk(): void
    {
        $this->syncWithDisk = true;
        Stream::copyToStream(
            $this->content,
            Stream::createFromFile($this->filename, 'w+')
        );
    }

    public function size(): int
    {
        return $this->content->getSize() ?? 0;
    }

    public function append(string $data): void
    {
        $this->syncWithDisk = false;
        $this->content->write($data);
    }

    public function read(int $length = 1): string
    {
        return $this->content->read($length);
    }

    public function readAll(): string
    {
        return $this->content->getContents();
    }

}
