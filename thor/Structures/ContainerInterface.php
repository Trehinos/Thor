<?php

namespace Thor\Structures;

/**
 * This is NOT the PSR's interface of Container.
 *
 * This file defines a container & composite interface.
 *
 * A composite can contains Items and other composites (as a composite IS an item).
 *
 * @package          Thor/Structures
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
interface ContainerInterface extends ItemInterface
{

    /**
     * Sets one item in the container.
     *
     * If another item with the same name is already in the container, it is replaced by the specified item.
     *
     * @param ItemInterface $child
     *
     * @return $this
     */
    public function setItem(ItemInterface $child): static;

    /**
     * Gets one item from the container.
     *
     * If no item matches the specified key, this method returns null.
     *
     * @param string $key
     *
     * @return ItemInterface|null
     */
    public function getItem(string $key): ?ItemInterface;

    /**
     * Removes one item in the container.
     *
     * Returns true if the item was successfully removed, or false otherwise.
     *
     * @param string $key
     *
     * @return bool
     */
    public function removeItem(string $key): bool;

    /**
     * Returns true if an item matches the specified key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasItem(string $key): bool;

    /**
     * Performs an operation on each item in the container.
     *
     * If $keys is specified, only performs the operation on the matched items.
     *
     * $operation signature : function (string $key, ContainerInterface|ItemInterface|null $value): mixed
     *
     * This function returns the list of $operation results.
     *
     * @param callable   $operation
     * @param array|null $keys
     *
     * @return array
     */
    public function eachItem(callable $operation, ?array $keys = null): array;

    /**
     * Copy the content of this Container into the specified Container.
     *
     * @param ContainerInterface $container
     * @param array|null         $keys
     *
     * @return $this
     */
    public function copyInto(ContainerInterface $container, ?array $keys = null): static;

}
