<?php

namespace Thor\Cli;

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

    /** CURSOR/COLORS STATES */
    public const MODE_RESET = 0;
    public const MODE_BRIGHT = 1;
    public const MODE_DIM = 2;
    public const MODE_UNDERSCORE = 3;
    public const MODE_BLINK = 5;
    public const MODE_REVERSE = 7;
    public const MODE_HIDDEN = 8;

    /** COLORS */
    private const FOREGROUND = 30;
    private const BACKGROUND = 40;

    public const COLOR_BLACK = 0;
    public const COLOR_RED = 1;
    public const COLOR_GREEN = 2;
    public const COLOR_YELLOW = 3;
    public const COLOR_BLUE = 4;
    public const COLOR_MAGENTA = 5;
    public const COLOR_CYAN = 6;
    public const COLOR_GRAY = 7;

    /** CLEAR SCREEN SEQUENCE */
    public const CLEAR = "\033[H\033[J";

    /** CURSOR CONTROL SEQUENCES */
    public const CURSOR_HOME = "\033[0K";
    public const CURSOR_UP = "\033[yA";
    public const CURSOR_DOWN = "\033[yB";
    public const CURSOR_RIGHT = "\033[xC";
    public const CURSOR_LEFT = "\033[xD";
    public const CURSOR_POS = "\033[y;xf";
    public const CURSOR_SAVE = "\033[s";
    public const CURSOR_UNSAVE = "\033[u";
    public const CURSOR_SAVEALL = "\0337";     # unused
    public const CURSOR_RESTORE = "\0338";     # unused

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
     * @param int $mode
     *
     * @return Console
     */
    public function mode(int $mode = self::MODE_RESET): self
    {
        echo self::COLOR_START . "$mode" . self::COLOR_END;

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
     * @param int      $fc
     * @param int|null $mode
     *
     * @return Console
     */
    public function fColor(int $fc = self::COLOR_GRAY, ?int $mode = null): self
    {
        $fcolor = self::FOREGROUND + $fc;
        $str_mode = (null === $mode ? '' : ";$mode");

        echo self::COLOR_START . "$fcolor$str_mode" . self::COLOR_END;

        return $this;
    }

    /**
     * Change Background color.
     *
     * @param int      $bc
     * @param int|null $mode
     *
     * @return Console
     */
    public function bColor(int $bc = self::COLOR_BLACK, ?int $mode = null): self
    {
        $bcolor = self::BACKGROUND + $bc;
        $str_mode = (null === $mode ? '' : ";$mode");

        echo self::COLOR_START . "$bcolor$str_mode" . self::COLOR_END;

        return $this;
    }

    /**
     * Moves the cursor up.
     *
     * @param int $y
     *
     * @return $this
     */
    public function moveUp(int $y): self
    {
        echo str_replace('y', $y, self::CURSOR_UP);

        return $this;
    }

    /**
     * Moves the cursor down.
     *
     * @param int $y
     *
     * @return $this
     */
    public function moveDown(int $y): self
    {
        echo str_replace('y', $y, self::CURSOR_DOWN);

        return $this;
    }

    /**
     * Moves the cursor left.
     *
     * @param int $x
     *
     * @return $this
     */
    public function moveLeft(int $x): self
    {
        echo str_replace('x', $x, self::CURSOR_LEFT);

        return $this;
    }

    /**
     * Moves the cursors right.
     *
     * @param int $x
     *
     * @return $this
     */
    public function moveRight(int $x): self
    {
        echo str_replace('x', $x, self::CURSOR_RIGHT);

        return $this;
    }

    /**
     * Locate the cursor at the specified row ($y) and column ($x).
     *
     * @param int $y
     * @param int $x
     *
     * @return Console
     */
    public function locate(int $y, int $x): self
    {
        echo str_replace(['x', ';y'], [$x, $y], self::CURSOR_POS);

        return $this;
    }

    /**
     * Locate the cursor at the specified row ($y) and column ($x).
     *
     * @return Console
     */
    public function home(): self
    {
        echo self::CURSOR_HOME;

        return $this;
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
        echo self::CURSOR_SAVE . $text . self::CURSOR_UNSAVE;

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

}
