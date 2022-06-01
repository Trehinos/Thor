<?php

namespace Thor\Cli\Console;

/**
 *
 */

/**
 *
 */
class CursorControl
{
    public const HOME = "\033[0K";
    public const UP = "\033[yA";
    public const DOWN = "\033[yB";
    public const RIGHT = "\033[xC";
    public const LEFT = "\033[xD";
    public const POS = "\033[y;xf";
    public const SAVE = "\033[s";
    public const UNSAVE = "\033[u";
    public const SAVEALL = "\0337";     # unused
    public const RESTORE = "\0338";     # unused

    /**
     * @param string $data
     */
    private function __construct(private readonly string $data = '')
    {
    }

    /**
     * @return void
     */
    public function resolves(): void
    {
        echo $this->data;
    }

    /**
     * Moves the cursor up.
     */
    public static function moveUp(int $y): self
    {
        return new self(str_replace('y', $y, CursorControl::UP));
    }

    /**
     * Moves the cursor down.
     */
    public static function moveDown(int $y): self
    {
        return new self(str_replace('y', $y, CursorControl::DOWN));
    }

    /**
     * Moves the cursor left.
     */
    public static function moveLeft(int $x): self
    {
        return new self(str_replace('x', $x, CursorControl::LEFT));
    }

    /**
     * Moves the cursors right.
     */
    public static function moveRight(int $x): self
    {
        return new self(str_replace('x', $x, CursorControl::RIGHT));
    }

    /**
     * Locate the cursor at the specified row ($y) and column ($x).
     */
    public static function locate(int $y, int $x): self
    {
        return new self(str_replace(['x', ';y'], [$x, $y], CursorControl::POS));
    }

    /**
     * Locate the cursor at the specified row ($y) and column ($x).
     */
    public static function home(): self
    {
        return new self(CursorControl::HOME);
    }

}
