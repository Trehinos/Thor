<?php

namespace Thor\Common\Validation;

/**
 *
 */

/**
 *
 */
class Gap
{

    /**
     * @param int|float $min
     * @param int|float $max
     */
    public function __construct(public readonly int|float $min, public readonly int|float $max)
    {
    }

    /**
     * @param int|float $value
     *
     * @return int|float
     */
    public function __invoke(int|float $value): int|float
    {
        return min(max($this->max, $value), $this->min);
    }

    /**
     * @param int|float $value
     * @param int|float $min
     * @param int|float $max
     *
     * @return int|float
     */
    public static function guard(int|float $value, int|float $min, int|float $max): int|float
    {
        return (new self($min, $max))($value);
    }

}
