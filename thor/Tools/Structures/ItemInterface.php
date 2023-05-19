<?php

namespace Thor\Tools\Structures;

/**
 * Interface for a Container Item.
 *
 * @package          Thor/Structures
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
interface ItemInterface
{

    /**
     * Gets the key of the Item.
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Gets the item's value.
     *
     * @return mixed
     */
    public function getValue(): mixed;

}
