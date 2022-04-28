<?php

namespace Thor\Web\Assets;

use DateInterval;
use Thor\Http\Uri;
use DateTimeImmutable;

class CachedAsset extends MergedAsset
{

    protected ?DateTimeImmutable $expires;

    public function __construct(
        AssetType $type,
        string $filename,
        Uri $uri,
        array $fileList = [],
        DateInterval|int|null $ttl = null
    ) {
        parent::__construct(
            $type,
            $filename,
            $uri,
            $fileList,
        );
        if (is_integer($ttl)) {
            $ttl = new DateInterval("PT{$ttl}M");
        }
        $this->expires = ($ttl === null)
            ? null
            : (new DateTimeImmutable())->add($ttl);
    }

    /**
     * Returns false if this item can't expire.
     *
     * @return DateTimeImmutable|false
     */
    public function expires(): DateTimeImmutable|false
    {
        return $this->expires ?? false;
    }

    /**
     * Returns true if the item has expired.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        return $this->expires !== null && $this->expires <= (new DateTimeImmutable());
    }

    public function cache(string $path): void
    {
        if ($this->hasExpired()) {
            // TODO
            $filename = "$path/{$this->name}";
        }
    }

    public function getFilename(): string
    {

        return parent::getFilename();
    }

}
