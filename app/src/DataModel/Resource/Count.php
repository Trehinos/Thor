<?php

namespace Evolution\DataModel\Resource;

use Evolution\Common\Units;

final class Count
{

    public function __construct(
        public readonly Resource $resource,
        private float            $count = 0.0,
        public readonly ?float   $max = null,
        protected ?Unit          $unit = null,
    ) {
        $this->unit ??= Units::unit();
    }

    public function get(): float|int
    {
        return $this->unit->digits === 0 ? intval($this->count) : $this->count;
    }

    public function __toString(): string
    {
        $max = $this->max ? ' / ' . $this->unit->get($this->max) : '';
        return $this->unit->get($this->count) . $max;
    }

    public function set(int $v): void
    {
        if ($this->max && $v > $this->max) {
            $v = $this->max;
        }
        if ($v < 0) {
            $v = 0;
        }

        $this->count = $v;
    }

    public function isValid(int $v): bool
    {
        if ($v < 0 || ($this->max && $v > $this->max)) {
            return false;
        }
        return true;
    }

    public function add(int $v): void
    {
        $this->set($this->get() + $v);
    }

}