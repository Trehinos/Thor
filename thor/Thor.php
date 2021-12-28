<?php

namespace Thor;

use Thor\Factories\Configurations;
use Thor\Configuration\Configuration;
use Thor\Configuration\ThorConfiguration;

/**
 * A general utility class for Thor.
 *
 * @package          Thor
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Thor
{

    private static ThorConfiguration|null $configuration = null;

    private function __construct()
    {
    }

    private static function config(): ThorConfiguration
    {
        global $thor_kernel;
        return self::$configuration ??= new ThorConfiguration($thor_kernel);
    }

    public static function version(): string
    {
        return self::config()->appVersion();
    }

    public static function versionName(): string
    {
        return self::config()->appVersionName();
    }

    public static function appName(): string
    {
        return self::config()->appName();
    }

    public static function vendor(): string
    {
        return self::config()->appVendor();
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
        return self::config()->env();
    }

}
