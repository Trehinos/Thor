<?php

namespace Thor\Tools\Structures;

/**
 * Container's item default implementation.
 *
 * @package          Thor/Structures
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class Item implements ItemInterface
{

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function __construct(private readonly string $key, protected mixed $value)
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
