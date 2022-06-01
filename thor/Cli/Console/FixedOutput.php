<?php

namespace Thor\Cli\Console;

/**
 *
 */

/**
 *
 */
class FixedOutput
{

    /**
     * @param string $text
     * @param int    $size
     * @param int    $direction
     */
    public function __construct(
        private readonly string $text = '',
        private readonly int $size = 10,
        private readonly int $direction = STR_PAD_RIGHT
    ) {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return str_pad($this->text, $this->size, ' ', $this->direction);
    }

}
