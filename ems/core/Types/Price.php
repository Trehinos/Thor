<?php

namespace Ems\Types;

use Ems\Packages\Sales\Currencies\Euro;
use Ems\Packages\Sales\Element\Currency;

readonly class Price
{

    public function __construct(
        public int $integerPart,
        public int $fractionalPart = 0,
        public Currency $unit = new Euro(),
        public int $precision = 2,
        public string $separator = ','
    ) {
    }

    public function __toString(): string
    {
        return "{$this->I()}{$this->separator}{$this->D()}";
    }

    public function toFloat(): float
    {
        return floatval("{$this->I()}.{$this->D()}");
    }

    private function I(): string
    {
        return number_format($this->integerPart, thousands_separator: ' ');
    }

    private function D(): string
    {
        return str_pad($this->fractionalPart, $this->precision, '0', STR_PAD_LEFT);
    }

}
