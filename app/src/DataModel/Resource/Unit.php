<?php

namespace Evolution\DataModel\Resource;

class Unit
{

    public function __construct(
        public string $name,
        public int $multiple = 0,
        public array $suite = [],
        public int $digits = 3,
    ) {}

    public function get(float $value): string
    {
        $unitIndex = 0;

        if ($this->multiple > 0) {
            $value = $value % $this->multiple;
            $unitIndex = intdiv($value, $this->multiple);
        }
        $unit = $this->suite[min(count($this->suite) - 1, $unitIndex)];
        if (count($this->suite) === 0) {
            $unit = $this->name;
        }

        return round($value, $this->digits) . $unit;
    }

}
