<?php

/**
 * @package Thor
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor;

final class Thor
{

    public const VERSION = '0.9-dev';
    public const VERSION_NAME = 'ω';

    private function __construct()
    {
    }

    private static ?Configuration $configuration = null;

    public static function getConfiguration(): Configuration
    {
        return self::$configuration ??= Configuration::getInstance();
    }

    public static function config(string $name, bool $staticResource = false): array
    {
        return self::getConfiguration()->loadConfig($name, $staticResource);
    }

    public static function getEnv(): Env
    {
        return Env::tryFrom(strtoupper(self::config('config')['env'] ?? 'dev'));
    }

    public static function isDev(): bool
    {
        return self::getEnv() === Env::DEV;
    }

    public static function appName(): string
    {
        return self::config('config')['app_name'] ?? '';
    }



}
