<?php

namespace Thor\Framework;

use Thor\Env;
use Thor\Framework\Configurations\ThorConfiguration;

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

    /**
     * @return ThorConfiguration
     */
    private static function config(): ThorConfiguration
    {
        global $thor_kernel;
        return self::$configuration ??= new ThorConfiguration($thor_kernel);
    }

    /**
     * @return string
     */
    public static function version(): string
    {
        return self::config()->appVersion();
    }

    /**
     * @return string
     */
    public static function versionName(): string
    {
        return self::config()->appVersionName();
    }

    /**
     * @return string
     */
    public static function appName(): string
    {
        return self::config()->appName();
    }

    /**
     * @return string
     */
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
     * Returns true if the current Thor's environment is Env::DEBUG or Env::DEV.
     *
     * @return bool
     */
    public static function isDebug(): bool
    {
        return self::isDev() || self::getEnv() === Env::DEBUG;
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
