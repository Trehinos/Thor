<?php

namespace Thor\Http;

final class Session
{

    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function abort(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_abort();
        }
    }

    public static function reset(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_reset();
        }
    }

    public static function write(string $name, mixed $value): void
    {
        $_SESSION[$name] = $value;
    }

    public static function read(string $name, mixed $default = null): mixed
    {
        return $_SESSION[$name] ?? $default;
    }

}
