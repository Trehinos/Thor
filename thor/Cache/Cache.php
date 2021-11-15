<?php

namespace Thor\Cache;

use DateInterval;
use Thor\Structures\Container;

class Cache extends Container implements CacheInterface
{

    public function __construct(private DateInterval|int|null $defaultTtl = null)
    {
        parent::__construct('Memory Cache');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->getItem($key)->getValue() : $default;
    }

    public function has(string $key): bool
    {
        return $this->hasItem($key) && !$this->hasExpired($key);
    }

    public function hasExpired(string $key): bool
    {
        return $this->getItem($key)?->hasExpired() ?? false;
    }

    public function multiple(iterable $items, callable $operation): bool
    {
        $result = false;
        foreach ($items as $key => $item) {
            $result = $result && $operation($key, $item);
        }
        return $result;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->setItem(new CacheItem($key, $value, $ttl));
        return true;
    }

    public function delete(string $key): bool
    {
        return $this->removeItem($key);
    }

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

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        return $this->multiple(
            $values,
            fn(string $key, mixed $item) => $this->set($key, $item, $ttl ?? $this->defaultTtl)
        );
    }

    public function deleteMultiple(iterable $keys): bool
    {
        return $this->multiple(
            $keys,
            fn(string $unused, string $key) => $this->delete($key)
        );
    }

}
