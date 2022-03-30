<?php

namespace Thor\Cli\Console;

enum Color: int
{
    private const FOREGROUND = 30;
    private const BACKGROUND = 40;

    case BLACK = 0;
    case RED = 1;
    case GREEN = 2;
    case YELLOW = 3;
    case BLUE = 4;
    case MAGENTA = 5;
    case CYAN = 6;
    case GRAY = 7;

    case FG_BLACK = 30;
    case FG_RED = 31;
    case FG_GREEN = 32;
    case FG_YELLOW = 33;
    case FG_BLUE = 34;
    case FG_MAGENTA = 35;
    case FG_CYAN = 36;
    case FG_GRAY = 37;

    case BG_BLACK = 40;
    case BG_RED = 41;
    case BG_GREEN = 42;
    case BG_YELLOW = 43;
    case BG_BLUE = 44;
    case BG_MAGENTA = 45;
    case BG_CYAN = 46;
    case BG_GRAY = 47;

    public function fg(): int
    {
        return $this->value + self::FOREGROUND;
    }

    public function bg(): int
    {
        return $this->value + self::BACKGROUND;
    }
}
