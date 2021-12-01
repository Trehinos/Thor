<?php

namespace Thor\Structures;

/**
 * Container's item default implementation.
 *
 * @package          Thor/Structures
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class Item implements ItemInterface
{

    public function __construct(private string $key, private mixed $value)
    {
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
