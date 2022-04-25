<?php

namespace Thor\Framework\Factories;

use Thor\Globals;
use Symfony\Component\Yaml\Yaml;
use Thor\Configuration\Configuration;
use Thor\Configuration\ThorConfiguration;
use Thor\Configuration\CommandsConfiguration;
use Thor\Framework\Configuration\TwigConfiguration;
use Thor\Framework\Configuration\LanguageDictionary;
use Thor\Framework\Configuration\RoutesConfiguration;
use Thor\Framework\Configuration\SecurityConfiguration;
use Thor\Framework\Configuration\DatabasesConfiguration;

/**
 * This class loads configuration files from thor/res/{static/config}/*yml files
 * and keep their in memory accessible statically.
 *
 * @package          Thor
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class Configurations
{

    private function __construct()
    {
    }

    /**
     * Gets the configuration of a daemon.
     *
     * If no filename is given, loads all daemons configurations.
     *
     * @param string|null $fileName
     *
     * @return Configuration
     */
    public static function getDaemonsConfig(?string $fileName = null): Configuration
    {
        $files = glob(Globals::STATIC_DIR . 'daemons/*.yml');
        $config = new Configuration();
        foreach ($files as $file) {
            if ($fileName && $file === $fileName) {
                $config[] = Yaml::parseFile($fileName);
            } elseif (null === $fileName) {
                $config[] = Yaml::parseFile($file);
            }
        }
        return $config;
    }

    /**
     * Loads the configuration for Web context (index.php entry point).
     */
    public static function getWebConfiguration(): Configuration
    {
        return self::getHttpConfiguration()->merge(
            new Configuration(
                [
                    'routes' => RoutesConfiguration::get('web'),
                    'twig'   => TwigConfiguration::get(),
                ]
            )
        );
    }

    /**
     * Loads the configuration for Http context (api.php entry point).
     */
    public static function getHttpConfiguration(): Configuration
    {
        return self::getBaseConfiguration()->merge(
            new Configuration(
                [
                    'security' => SecurityConfiguration::get(),
                    'routes'   => RoutesConfiguration::get('api'),
                ]
            )
        );
    }

    public static function getBaseConfiguration(): Configuration
    {
        global $thor_kernel;
        return new Configuration(
            [
                'config' => $thorConfig = ThorConfiguration::get($thor_kernel),
                'database' => DatabasesConfiguration::get(),
                'language' => LanguageDictionary::get($thorConfig->lang),
            ]
        );
    }

    /**
     * Loads the configuration for Console context (thor.php entry point).
     *
     * @return Configuration
     */
    public static function getConsoleConfiguration(): Configuration
    {
        return self::getBaseConfiguration()->merge(
            new Configuration(
                [
                    'commands' => new CommandsConfiguration(),
                ]
            )
        );
    }

}
