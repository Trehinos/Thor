<?php

namespace Ems\Packages\Sales\Element;

class Identifier
{

    private int $current;

    public function __construct(
        public readonly string $prefix,
        public readonly int $begin,
        private readonly int $length = 3
    ) {
        $this->current = $this->begin;
    }

    public function __toString(): string
    {
        $c = str_pad($this->current, $this->length, '0', STR_PAD_LEFT);
        return "$this->prefix$c";
    }

}
