<?php

namespace Thor\Structures;

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

    /**
     * @var ItemInterface[]
     */
    private array $data = [];

    public function __construct(string $key)
    {
        parent::__construct($key, null);
    }

    /**
     * Gets the container items.
     *
     * @return ItemInterface[]
     */
    public function getValue(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function setItem(ItemInterface $child): static
    {
        $this->data[$child->getKey()] = $child;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getItem(string $key): ?ItemInterface
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function hasItem(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @inheritDoc
     */
    public function eachItem(callable $operation, ?array $keys = null): array
    {
        return array_map(
            function (string $key, ContainerInterface|ItemInterface|null $value) use ($operation, $keys) {
                if ($keys !== null && !in_array($key, $keys)) {
                    return $value;
                }
                return $operation($key, $value);
            },
            array_keys($this->data),
            array_values($this->data)
        );
    }

    /**
     * @inheritDoc
     */
    public function copy(ContainerInterface $container): static
    {
        $this->eachItem(
            function (string $key, ?ItemInterface $value) use ($container) {
                $container->setItem($value);
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

        $this->data[$key] = null;
        unset($this->data[$key]);
        return true;
    }
}
