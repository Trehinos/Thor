<?php

namespace Thor\Web\Assets;

class AssetsList implements \ArrayAccess
{

    /**
     * @var Asset[]
     */
    private array $assets = [];

    public function __construct() {
    }

    public function addAsset(Asset $asset): self
    {
        $this[$asset->identifier] = $asset;
        return $this;
    }

    public function offsetExists(mixed $offset): bool
    {
        return ($this->assets[$offset] ?? false) !== false;
    }

    public function offsetGet(mixed $offset): ?Asset
    {
        return $this->assets[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->assets[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->assets[$offset] = null;
        unset($this->assets[$offset]);
    }
}
