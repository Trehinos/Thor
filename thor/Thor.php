<?php

namespace Thor;

/**
 * A general utility class for Thor.
 *
 * @package          Thor
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Thor
{

    private static ?Configuration $configuration = null;

    private function __construct()
    {
    }

    public static function version(): string
    {
        return self::config('config')['app_version'] ?? '';
    }

    /**
     * Gets the configuration from a file in the resources' folder.
     *
     * @param string $name
     * @param bool   $staticResource If false (default), search in the res/config/ folder.
     *                               If true, search in the res/static/ folder.
     *
     *
     * @return array
     */
    public static function config(string $name, bool $staticResource = false): array
    {
        return self::getConfiguration()->loadConfig($name, $staticResource);
    }

    /**
     * Gets the static Configuration object.
     *
     * @return Configuration
     */
    public static function getConfiguration(): Configuration
    {
        return self::$configuration ??= Configuration::getInstance();
    }

    public static function versionName(): string
    {
        return self::config('config')['app_version_name'] ?? '';
    }

    public static function appName(): string
    {
        return self::config('config')['app_name'] ?? '';
    }

    public static function vendor(): string
    {
        return self::config('config')['app_vendor'] ?? '';
    }

    /**
     * Returns true if the current Thor's environment is Env::DEV.
     *
     * @return bool
     */
    public static function isDev(): bool
    {
        return self::getEnv() === Env::DEV;
    }

    /**
     * Gets the current Thor's environment.
     *
     * @return Env
     */
    public static function getEnv(): Env
    {
        return Env::tryFrom(strtoupper(self::config('config')['env'] ?? 'dev'));
    }

}
