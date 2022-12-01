<?php

namespace Thor\Structures\Biology;

use Exception;

/**
 * Describe an ADN codon or an ADN sequence.
 */
final class Adn
{

    public function __construct(private array $nucleus = [])
    {
    }

    /**
     * Returns a completly random ADN chunk.
     *
     * @param int $size
     *
     * @return static
     */
    public static function random(int $size): self
    {
        return new self(
            array_map(
                fn() => Nucleus::cases()[random_int(0, 3)],
                array_fill(0, $size, null)
            )
        );
    }

    /**
     * Returns a new instance of `Adn` from an `"ACGT"` string.
     *
     * This method is **case insensitive**.
     *
     * @param string $adnString
     *
     * @return static
     * @throws Exception
     */
    public static function fromString(string $adnString): self
    {
        return new self(
            array_map(
                fn (string $char) => match ($char) {
                    'A', 'a' => Nucleus::A,
                    'C', 'c' => Nucleus::C,
                    'G', 'g' => Nucleus::G,
                    'T', 't' => Nucleus::T,
                    default => throw new Exception("Invalid character $char in ADN sequence.")
                },
                str_split($adnString)
            )
        );
    }

    /**
     * Appends `$extension` to `$base` and returns `$base`.
     *
     * @param Adn $base
     * @param Adn $extension
     *
     * @return static $base
     */
    public static function merge(self $base, self $extension): self
    {
        return $base->append($extension);
    }

    /**
     * Returns the ADN sequence's size.
     *
     * @return int
     */
    public function size(): int
    {
        return count($this->nucleus);
    }

    /**
     * Appends the ADN chunk to this instance.
     *
     * @param Adn $chunk
     *
     * @return $this
     */
    public function append(self $chunk): self
    {
        $this->nucleus = array_merge($this->nucleus, $chunk->nucleus);

        return $this;
    }

    /**
     * Returns a new instance of `Adn` with a sequence from $offset to `$offset + $length` or the end if `$offset === null`.
     *
     * @param int  $offset
     * @param ?int $length
     *
     * @return static
     */
    public function select(int $offset, ?int $length = null): self
    {
        return new self(array_slice($this->nucleus, $offset, $length));
    }

}
