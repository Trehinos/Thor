<?php

namespace Thor\Structures;

use Thor\Structures\Collection\Collection;

/**
 * This is NOT the PSR's interface of Container.
 *
 * This file defines a container & composite implementation.
 *
 * A composite can contains Items and other composites (as a composite IS an item).
 *
 * @package          Thor/Structures
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class Container extends Item implements ContainerInterface
{

    /** @var Collection<ItemInterface> $value */

    /**
     * @param string                    $key
     * @param Collection<ItemInterface> $data
     */
    public function __construct(string $key, Collection $data = new Collection())
    {
        parent::__construct($key, $data);
    }

    /**
     * Gets the container items.
     *
     * @return Collection<ItemInterface>
     */
    public function getValue(): Collection
    {
        return parent::getValue();
    }

    /**
     * @inheritDoc
     */
    public function setItem(ItemInterface $child): static
    {
        $this->value[$child->getKey()] = $child;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getItem(string $key): ?ItemInterface
    {
        return $this->value[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function hasItem(string $key): bool
    {
        return $this->value->keyExists($key);
    }

    /**
     * @inheritDoc
     */
    public function eachItem(callable $operation, ?array $keys = null): array
    {
        return array_map(
            function (string|int|null $key, ContainerInterface|ItemInterface|null $item) use ($operation, $keys) {
                if ($keys !== null && !in_array($key, $keys)) {
                    return $item;
                }
                return $operation($key, $item);
            },
            $this->value->keys()->toArray(),
            $this->value->values()->toArray()
        );
    }

    /**
     * @inheritDoc
     */
    public function copyInto(ContainerInterface $container, ?array $keys = null): static
    {
        $this->eachItem(
            function (string $key, ContainerInterface|ItemInterface|null $item) use ($container, $keys) {
                if ($keys !== null && !in_array($key, $keys)) {
                    return;
                }
                if ($item instanceof ContainerInterface) {
                    $newContainer = new Container($key);
                    $item->copyInto($newContainer);
                    $item = $newContainer;
                } elseif ($item instanceof ItemInterface) {
                    $newItem = new Item($key, $item->getValue());
                    $item = $newItem;
                }
                $container->setItem($item);
            }
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeItem(string $key): bool
    {
        if (!$this->hasItem($key)) {
            return false;
        }

        unset($this->value[$key]);
        return true;
    }
}
