<?php

namespace Thor;

final class Globals
{
    const CODE_DIR = __DIR__ . '/../../';
    const ENGINE_DIR = __DIR__ . '/../';
    const PHP_DIR = __DIR__ . '/';
    const CONFIG_DIR = self::ENGINE_DIR . 'config/';

    static function post(string $name, $default = null, ?int $filter = null)
    {
        if (null !== $filter) {
            return (false === ($filtered = filter_input(INPUT_POST, $name, $filter)))
                ? $default
                : $filtered;
        }

        return $_POST[$name] ?? $default;
    }

    static function get(string $name, $default = null, ?int $filter = null)
    {
        if (null !== $filter) {
            return (false === ($filtered = filter_input(INPUT_GET, $name, $filter)))
                ? $default
                : $filtered;
        }

        return $_GET[$name] ?? $default;
    }

    static function readCookie(string $name, string $default = '', ?int $filter = null)
    {
        if (null !== $filter) {
            return (false === ($filtered = filter_input(INPUT_COOKIE, $name, $filter)))
                ? $default
                : $filtered;
        }

        return $_COOKIE[$name] ?? $default;
    }

    static function writeCookie(string $name, string $value)
    {
        setcookie($name, $value);
    }

    static function writeCookieArray(string $name, array $value)
    {
        foreach ($value as $key => $v) {
            self::writeCookie("$name[$key]", $v);
        }
    }

    static function readSession(string $name, $default = null, ?int $filter = null)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (null !== $filter) {
            return (false === ($filtered = filter_input(INPUT_SESSION, $name, $filter)))
                ? $default
                : $filtered;
        }

        return $_SESSION[$name] ?? $default;
    }

    static function writeSession(string $name, $value)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION[$name] = $value;
    }
}
