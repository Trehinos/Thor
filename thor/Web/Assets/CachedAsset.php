<?php

namespace Thor\Web\Assets;

use Thor\Stream\StreamInterface;

class CachedAsset extends Asset
{
    public function __construct(
        AssetType $type,
        string $filename,
        ?StreamInterface $inputFile = null,
        protected ?StreamInterface $cacheFile = null
    ) {
        parent::__construct($type, $filename, $inputFile);
    }

    public function getCachedContent(): string
    {
        return ($this->cacheFile ?? $this->file ?? null)?->getContents() ?? '';
    }

}
