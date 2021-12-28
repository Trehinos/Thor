<?php

namespace Thor\Configuration;

use Thor\Globals;
use Symfony\Component\Yaml\Yaml;

class ConfigurationFromFile extends Configuration
{

    private static array $configurations = [];

    public static function get(mixed ...$args): static
    {
        return static::$configurations[static::class] ??= new static(...$args);
    }

    public function __construct(string $filename, bool $isStatic = false)
    {
        parent::__construct(self::loadYml($filename, $isStatic));
    }

    /**
     * Gets the configuration from a file in the resources' folder.
     *
     * @param string $name
     * @param bool   $staticResource If false (default), search in the res/config/ folder.
     *                               If true, search in the res/static/ folder.
     *
     * @return static
     */
    public static function fromFile(string $name, bool $staticResource = false): static
    {
        return new static(self::loadYml($name, $staticResource));
    }

    public static function loadYml(string $name, bool $staticResource = false): array
    {
        return Yaml::parseFile(($staticResource ? Globals::STATIC_DIR : Globals::CONFIG_DIR) . "$name.yml");
    }

}
