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
            while ($value >= $this->multiple && $unitIndex < count($this->suite)) {
                $value = $value / $this->multiple;
                $unitIndex++;
            }
        }
        $unit = $this->suite[$unitIndex];
        if (count($this->suite) === 0) {
            $unit = $this->name;
        }

        if (abs($value) > 1) {
            $digits = $this->digits - strlen(intval($value));
        } else {
            $digits = $this->digits;
        }

        return number_format($value, $digits) . $unit;
    }

}
