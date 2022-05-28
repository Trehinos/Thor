<?php

namespace Thor\Web\Assets;

use DateInterval;
use Thor\Http\Uri;
use DateTimeImmutable;
use Thor\Debug\Logger;
use Thor\Stream\Stream;
use Thor\Debug\LogLevel;

class CachedAsset extends MergedAsset
{

    public function __construct(
        AssetType $type,
        string $filename,
        Uri $uri,
        array $fileList = [],
        protected DateInterval|int|null $ttl = null,
        protected string $cacheHostPath = '',
        protected string $cacheFilePath = '',
    ) {
        parent::__construct(
            $type,
            $filename,
            $uri,
            $fileList,
        );
        $this->uri = $uri->withPath($this->cacheHostPath . $this->getFilename());
        $this->setNode();
        if (is_integer($this->ttl)) {
            $this->ttl = new DateInterval("PT{$this->ttl}M");
        }
        $this->cache();
    }

    /**
     * Returns true if the item has expired.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        return !file_exists($this->getCacheFilename())
               || (
                   $this->ttl !== null &&
                   (new DateTimeImmutable())->sub($this->ttl) > DateTimeImmutable::createFromFormat(
                       'U',
                       filemtime($this->getCacheFilename())
                   )
               );
    }

    public function getCacheFilename(): string
    {
        return "{$this->cacheFilePath}/{$this->getFilename()}";
    }

    public function cache(): void
    {
        if ($this->hasExpired()) {
            Logger::write("Writing web cache for file {$this->filename}", LogLevel::INFO);
            Stream::createFromFile($this->getCacheFilename(), 'w')->write($this->getContent());
        }
    }

    public function getFilename(): string
    {
        return "{$this->name}.{$this->type->getExtension()}";
    }

}
