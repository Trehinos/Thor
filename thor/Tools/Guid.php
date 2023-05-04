<?php

namespace Thor\Tools;

/**
 *
 */

/**
 *
 */
final class Guid
{

    private string $guid;

    /**
     * @param int $size
     *
     * @throws \Exception
     */
    public function __construct(private readonly int $size = 16)
    {
        $this->guid = random_bytes($size);
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->guid;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $hex = $this->getHex();
        return strtoupper(
            match (true) {
                $this->size <= 4  => $hex,
                $this->size <= 12 => substr($hex, 0, 8) . '-' . substr($hex, 8),
                default           => substr($hex, 0, 8) .
                    '-' . substr($hex, 8, 4) .
                    '-' . substr($hex, 12, 4) .
                    '-' . substr($hex, 16, 4) .
                    '-' . implode('-', str_split(substr($hex, 20), 12))
            }
        );
    }

    /**
     * @return string
     */
    public function getBase64(): string
    {
        return base64_encode($this->guid);
    }

    /**
     * @return string
     */
    public function getHex(): string
    {
        return bin2hex($this->guid);
    }

    /**
     * @param int $size
     *
     * @return string
     * @throws \Exception
     * @throws \Exception
     */
    public static function hex(int $size = 16): string
    {
        $uuid = new self($size);
        return $uuid->getHex();
    }

    /**
     * @param int $size
     *
     * @return string
     * @throws \Exception
     * @throws \Exception
     */
    public static function hexString(int $size = 16): string
    {
        $uuid = new self($size);
        return "$uuid";
    }

    /**
     * @param int $size
     *
     * @return string
     * @throws \Exception
     * @throws \Exception
     */
    public static function base64(int $size = 16): string
    {
        $uuid = new self($size);
        return $uuid->getBase64();
    }

}
