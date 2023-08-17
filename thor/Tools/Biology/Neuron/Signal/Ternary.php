<?php

namespace Thor\Tools\Biology\Neuron\Signal;

enum Ternary: int
{

    case NEG = -1;
    case NIL = 0;
    case POS = 1;

    public function toChar(): string
    {
        return match ($this) {
            self::NEG => '-',
            self::NIL => '0',
            self::POS => '+',
        };
    }
}
