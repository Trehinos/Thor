<?php

namespace Ems\Types;

class Rate implements AmountInterface
{
    public function __construct(
        public float $value,
        public int $precision = 2,
        public string $separator = ','
    ) {
    }

    public function __toString(): string
    {
        return number_format(round($this->value, $this->precision), $this->precision, $this->separator);
    }

    public function toFloat(): float
    {
        return $this->value / 100;
    }

    public function alter(float $f): float
    {
        return  $f * $this->toFloat();
    }

}
