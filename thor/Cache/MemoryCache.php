<?php

namespace Thor\Cache;

use DateTime;
use DateInterval;
use DateTimeImmutable;

class MemoryCache implements CacheInterface
{

    public array $items = [];

    public function __construct()
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->items[$key]['value'] : $default;
    }

    public function has(string $key): bool
    {
        return $this->hasKey($key) && $this->hasNotExpired($key);
    }

    public function hasNotExpired(string $key): bool
    {
        return $this->items[$key]['expires'] === null
            || $this->items[$key]['expires'] > (new DateTimeImmutable());
    }

    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->items);
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
        $this->items[$key] = [
            'expires' => match (true) {
                $ttl === null => null,
                is_int($ttl) => (new DateTime())->add(new DateInterval("PT{$ttl}S")),
                default => (new DateTime())->add($ttl)
            },
            'value' => $value
        ];
        return true;
    }

    public function delete(string $key): bool
    {
        if (!$this->hasKey($key)) {
            return false;
        }
        $this->items[$key] = null;
        unset($this->items[$key]);
        return true;
    }

    public function clear(): bool
    {
        $this->items = [];
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $multiple = [];
        $this->multiple(
            $keys,
            function (string $unused, string $key) use (&$multiple) {
                $multiple[$key] = $this->get($key);
            }
        );
        return $multiple;
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        return $this->multiple(
            $values,
            fn (string $key, mixed $item) => $this->set($key, $item)
        );
    }

    public function deleteMultiple(iterable $keys): bool
    {
        return $this->multiple(
            $keys,
            fn (string $unused, string $key) => $this->delete($key)
        );
    }
}
