<?php

namespace Thor\Web\Assets;

use DateInterval;
use Thor\Cache\Cache;

final class AssetsCache extends Cache
{

    public function __construct(private string $path, DateInterval|int|null $defaultTtl = null)
    {
        parent::__construct($defaultTtl);
    }

    public function readFromPath(): bool
    {
        $this->clear();
        //$list = Asset

        return true;
    }

    public function write(Asset $asset): void
    {

    }

}
