<?php

namespace Thor\Message\Headers;

use ArrayAccess;

/**
 * This class represents an email part headers.
 */
final class Headers implements ArrayAccess
{

    private array $headers;

    public function __construct()
    {
        $this->headers = [];
    }

    public static function encode(string $text, string $charset = 'utf-8'): string
    {
        $encoded = trim(base64_encode($text), "=");
        return "=?{$charset}?b?$encoded?=";
    }

    public function merge(Headers|array $h): void
    {
        foreach ($h as $hName => $hValue) {
            $this->headers[$hName] = $hValue;
        }
    }

    public function toArray(): array
    {
        return $this->headers;
    }

    public function __toString(): string
    {
        if (empty($this->headers)) {
            return '';
        }

        return implode(
                "\r\n",
                array_map(
                    fn(string $headerName, array|string|null $headerValue) => self::headerToString(
                        $headerName,
                        $headerValue
                    ),
                    array_keys($this->headers),
                    array_values($this->headers)
                )
            ) . "\r\n";
    }

    public static function headerToString(string $headerName, array|string|null $headerValue): string
    {
        if (is_array($headerValue)) {
            $headerValue = implode(', ', $headerValue);
        }
        return "$headerName: $headerValue";
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->headers);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->headers[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->headers[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->headers[$offset] = null;
        unset($this->headers[$offset]);
    }

}
