<?php

namespace Thor\Cache;

use DateInterval;
use DateTimeImmutable;
use Thor\Structures\Item;

class CacheItem extends Item
{

    private ?DateTimeImmutable $expires = null;

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
     * @return ?DateTimeImmutable
     */
    public function expires(): ?DateTimeImmutable
    {
        return $this->expires;
    }

    public function hasExpired(): bool
    {
        return $this->expires !== null && $this->expires <= (new DateTimeImmutable());
    }

}
