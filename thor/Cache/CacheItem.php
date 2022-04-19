<?php

namespace Thor\Cache;

use DateInterval;
use DateTimeImmutable;
use Thor\Structures\Item;

/**
 * This class represent an element of a Thor\Cache\Cache object.
 *
 * @internal         used and never exposed by Thor\Cache\Cache
 * @package          Thor/Cache
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class CacheItem extends Item
{

    private ?DateTimeImmutable $expires = null;

    /**
     * Constructs a new CacheItem.
     * .
     *
     * @param string                $key
     * @param mixed                 $value
     * @param DateInterval|int|null $ttl
     */
    public function __construct(string $key, mixed $value, DateInterval|int|null $ttl = null)
    {
        parent::__construct($key, $value);
        if (is_int($ttl)) {
            $this->expires = (new DateTimeImmutable())->add(new DateInterval("PT{$ttl}S"));
        } elseif ($ttl instanceof DateInterval) {
            $this->expires = (new DateTimeImmutable())->add($ttl);
        }
    }

    /**
     * Returns false if this item can't expire.
     *
     * @return DateTimeImmutable|false
     */
    public function expires(): ?DateTimeImmutable
    {
        return $this->expires ?? false;
    }

    /**
     * Returns true if the item has expired.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        return $this->expires !== null && $this->expires <= (new DateTimeImmutable());
    }

}
