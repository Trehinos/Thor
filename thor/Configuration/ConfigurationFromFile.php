<?php

namespace Thor\Configuration;

use Thor\Globals;
use Symfony\Component\Yaml\Yaml;

/**
 * @uses app/res/static
 */
class ConfigurationFromFile extends Configuration
{

    private static array $configurations = [];

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    final public static function get(mixed ...$args): static
    {
        return static::$configurations[static::class] ??= new static(...$args);
    }

    /**
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        parent::__construct(self::loadYml($filename));
    }

    /**
     * Gets the configuration from a file in the config folder.
     *
     * @param string $name
     *
     * @return static
     */
    public static function fromFile(string $name): static
    {
        return new static($name);
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public static function loadYml(string $name): array
    {
        return Yaml::parseFile(Globals::CONFIG_DIR . "$name.yml");
    }

    /**
     * @param Configuration $configuration
     * @param string        $name
     * @param bool          $staticResource
     *
     * @return bool
     */
    public static function writeTo(Configuration $configuration, string $name, bool $staticResource = false): bool
    {
        $str = Yaml::dump(
            $configuration->getArrayCopy(),
            6,
            flags: Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_NULL_AS_TILDE
        );
        return file_put_contents(
                   ($staticResource ? Globals::STATIC_DIR : Globals::CONFIG_DIR) . "$name.yml",
                   $str
               ) !== false;
    }

    public function write(string $name, bool $staticResource = false): bool
    {
        $str = Yaml::dump(
            $this->getArrayCopy(),
            6,
            flags: Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_NULL_AS_TILDE
        );
        return file_put_contents(
                ($staticResource ? Globals::STATIC_DIR : Globals::CONFIG_DIR) . "$name.yml",
                $str
            ) !== false;
    }

}
