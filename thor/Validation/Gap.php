<?php

namespace Thor\Validation;

class Gap
{

    public function __construct(public readonly int|float $min, public readonly int|float $max)
    {
    }

    public function __invoke(int|float $value): int|float
    {
        return min(max($this->max, $value), $this->min);
    }

    public static function guard(int|float $value, int|float $min, int|float $max): int|float
    {
        return (new self($min, $max))($value);
    }

    public static function test(): void
    {
        $markGap = new self(0, 20);
        $mark = $markGap(18);

        $mark = self::guard(18, 0, 20);
    }

}
