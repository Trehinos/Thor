<?php

namespace Thor\Cli\Console;

use Stringable;
use InvalidArgumentException;

/**
 * A class to control Cli output.
 *
 * @package          Thor/Cli
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class Console
{

    /** COLOR SEQUENCE */
    public const COLOR_START = "\033[";
    public const COLOR_END = 'm';

    /** CLEAR SCREEN SEQUENCE */
    public const CLEAR = "\033[H\033[J";

    /**
     * Constructs a new Console with default mode.
     */
    public function __construct()
    {
        $this->mode();
    }

    /**
     * Changes Foreground and Background colors.
     *
     * @param Mode $mode
     *
     * @return Console
     */
    public function mode(Mode $mode = Mode::RESET): self
    {
        echo self::COLOR_START . $mode->value . self::COLOR_END;

        return $this;
    }

    /**
     * Reset the cursor
     */
    public function __destruct()
    {
        $this->mode();
    }

    /**
     * Change Foreground color.
     *
     * @param Color      $color
     * @param Mode|null $mode
     *
     * @return Console
     */
    public function color(Color $color = Color::FG_GRAY, ?Mode $mode = null): self
    {
        //$mode ??= Mode::tryFrom($this->mode);
        $str_mode = (null === $mode ? '' : "{$mode->value};");
        echo self::COLOR_START . "$str_mode{$color->value}" . self::COLOR_END;

        return $this;
    }

    /**
     * Change Foreground color.
     *
     * @param Color      $fc
     * @param Mode|null $mode
     *
     * @return Console
     */
    public function fColor(Color $fc = Color::GRAY, ?Mode $mode = null): self
    {
        return $this->color($fc->fg(), $mode);
    }

    /**
     * Change Background color.
     *
     * @param Color      $bc
     * @param Mode|null $mode
     *
     * @return Console
     */
    public function bColor(Color $bc = Color::BLACK, ?Mode $mode = null): self
    {
        return $this->color($bc->bg(), $mode);
    }


    /**
     * Clear the screen
     */
    public function clear(): self
    {
        echo self::CLEAR;

        return $this;
    }

    /**
     * Print a text on the screen.
     *
     * @param string $text
     *
     * @return Console
     */
    public function write(string $text): self
    {
        echo $text;

        return $this;
    }

    /**
     * Print a text on the screen.
     *
     * @param string $text
     * @param int    $minSize
     * @param int    $direction
     *
     * @return Console
     */
    public function writeFix(string $text, int $minSize, int $direction = STR_PAD_RIGHT): self
    {
        echo str_pad($text, $minSize, ' ', $direction);

        return $this;
    }

    /**
     * Print a text on the screen. And locate the cursor at the beginning of the same line.
     *
     * @param string $text
     *
     * @return Console
     */
    public function writeInline(string $text): self
    {
        echo CursorControl::SAVE . $text . CursorControl::UNSAVE;

        return $this;
    }

    /**
     * Print a text on the screen. Then, print a new line.
     *
     * @param string $text
     *
     * @return Console
     */
    public function writeln(string $text = ''): self
    {
        echo "$text\n";

        return $this;
    }

    /**
     * Write all the texts with specified modes/colors.
     *
     * @param Color|Mode|CursorControl|Stringable|string ...$elements
     *
     * @return $this
     */
    public function echoes(Color|Mode|CursorControl|Stringable|string ...$elements): self
    {
        foreach ($elements as $index => $element) {
            if ($element instanceof Color) {
                $this->color($element);
            } elseif ($element instanceof Mode) {
                $this->mode($element);
            } elseif ($element instanceof CursorControl) {
                $element->resolves();
            } elseif ($element instanceof Stringable || is_string($element)) {
                $this->write("$element");
            } else {
                throw new InvalidArgumentException("Invalid argument \{$index} of type " . gettype($element) . " provided in Console::echoes().");
            }
        }

        return $this->mode();
    }

}
