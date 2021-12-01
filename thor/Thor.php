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

    public const VERSION = '0.9-dev';
    public const VERSION_NAME = 'ω';
    private static ?Configuration $configuration = null;

    private function __construct()
    {
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

    /**
     * Returns the configured app_name.
     *
     * @return string
     */
    public static function appName(): string
    {
        return self::config('config')['app_name'] ?? '';
    }

}
