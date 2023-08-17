<?php

namespace Thor\Debug;

final class Debug
{
    public static function dump(mixed $var): void
    {
        dump($var);
    }

    public static function log(string $message, LogLevel $level = LogLevel::DEBUG): void
    {
        Logger::write($message, $level);
    }

    public static function print(string $message, LogLevel $level = LogLevel::DEBUG): void
    {
        Logger::write($message, $level, print: true);
    }

    public static function exception(\Throwable $t): void
    {
        Logger::logThrowable($t);
    }

    public static function caught(callable $f, mixed ...$args): bool
    {
        try {
            $f(...$args);
        } catch (\Throwable $t) {
            self::exception($t);
            return false;
        }
        return true;
    }

}
