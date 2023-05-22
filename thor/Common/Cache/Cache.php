<?php

namespace Thor\Common\Cache;

use DateInterval;
use Thor\Tools\Structures\Container;

/**
 * This class represents a memory cache and a container.
 *
 * As this class never effectively persists data in persistent memory, it should not be used as is.
 * It can be used as a temporary memory.
 *
 * @package          Thor/Cache
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class Cache extends Container implements CacheInterface
{

    /**
     * Construct a memory cache.
     *
     * The container will be named "Memory Cache".
     *
     * @param DateInterval|int|null $defaultTtl
     */
    public function __construct(private DateInterval|int|null $defaultTtl = null)
    {
        parent::__construct('Memory Cache');
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->getItem($key)->getValue() : $default;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->hasItem($key) && !$this->hasExpired($key);
    }

    /**
     * Returns true if the cache item has expired.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasExpired(string $key): bool
    {
        $item = $this->getItem($key);
        if (null === $item) {
            return true;
        }
        if ($item instanceof CacheItem) {
            return $item->hasExpired();
        }
        return false;
    }

    /**
     * Executes the callable for each element in $items.
     *
     * $operation :
     * ```php
     * fn ($key, $item): bool
     * ```
     *
     * Returns (true &&= $operation())
     *
     * @param iterable $items
     * @param callable $operation
     *
     * @return bool
     */
    public function multiple(iterable $items, callable $operation): bool
    {
        $result = true;
        foreach ($items as $key => $item) {
            $result = $result && $operation($key, $item);
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->setItem(new CacheItem($key, $value, $ttl ?? $this->defaultTtl));
        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        return $this->removeItem($key);
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $ret = true;
        $this->eachItem(
            function (string $key) use (&$ret) {
                $ret = $ret && $this->delete($key);
            }
        );
        return $ret;
    }

    /**
     * @inheritDoc
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $multiple = [];
        $this->multiple(
            $keys,
            function (string $unused, string $key) use (&$multiple, $default) {
                $multiple[$key] = $this->get($key, $default);
            }
        );
        return $multiple;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        return $this->multiple(
            $values,
            fn(string $key, mixed $item) => $this->set($key, $item, $ttl ?? $this->defaultTtl)
        );
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(iterable $keys): bool
    {
        return $this->multiple(
            $keys,
            fn(string $unused, string $key) => $this->delete($key)
        );
    }

}
