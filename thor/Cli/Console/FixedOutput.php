<?php

namespace Thor\Cli\Console;

class FixedOutput
{

    public function __construct(
        private readonly string $text = '',
        private readonly int $size = 10,
        private readonly int $direction = STR_PAD_RIGHT
    ) {
    }

    public function __toString(): string
    {
        return str_pad($this->text, $this->size, ' ', $this->direction);
    }

}
