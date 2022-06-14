<?php

namespace Thor\Cli\Console;

/**
 *
 */

/**
 *
 */
enum Color: int
{
    private const FOREGROUND = 30;
    private const BACKGROUND = 40;

    /**
     * `Color::FG_BLACK === Color::BLACK->fg()` && `Color::BG_BLACK === Color::BLACK->bg()`
     */
    case BLACK = 0;

    /**
     * `Color::FG_RED === Color::RED->fg()` && `Color::BG_RED === Color::RED->bg()`
     */
    case RED = 1;

    /**
     * `Color::FG_GREEN === Color::GREEN->fg()` && `Color::BG_GREEN === Color::GREEN->bg()`
     */
    case GREEN = 2;

    /**
     * `Color::FG_YELLOW === Color::YELLOW->fg()` && `Color::BG_YELLOW === Color::YELLOW->bg()`
     */
    case YELLOW = 3;

    /**
     * `Color::FG_BLUE === Color::BLUE->fg()` && `Color::BG_BLUE === Color::BLUE->bg()`
     */
    case BLUE = 4;

    /**
     * `Color::FG_MAGENTA === Color::MAGENTA->fg()` && `Color::BG_MAGENTA === Color::MAGENTA->bg()`
     */
    case MAGENTA = 5;

    /**
     * `Color::FG_CYAN === Color::CYAN->fg()` && `Color::BG_CYAN === Color::CYAN->bg()`
     */
    case CYAN = 6;

    /**
     * `Color::FG_GRAY === Color::GRAY->fg()` && `Color::BG_GRAY === Color::GRAY->bg()`
     */
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

    public function fg(): self
    {
        return self::tryFrom($this->value + self::FOREGROUND);
    }

    public function bg(): self
    {
        return self::tryFrom($this->value + self::BACKGROUND);
    }
}
