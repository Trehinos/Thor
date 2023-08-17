<?php

namespace Thor\Configuration;

use Thor\Globals;
use Symfony\Component\Yaml\Yaml;

/**
 * @uses app/res/static
 */
class ConfigurationFromResource extends Configuration
{

    private static array $configurations = [];

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public static function get(mixed ...$args): static
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
        return Yaml::parseFile(Globals::STATIC_DIR . "$name.yml");
    }

    /**
     * @param Configuration $configuration
     * @param string $name
     *
     * @return bool
     */
    public static function writeTo(Configuration $configuration, string $name): bool
    {
        $str = Yaml::dump(
            $configuration->getArrayCopy(),
            6,
            flags: Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_NULL_AS_TILDE
        );
        return file_put_contents(Globals::STATIC_DIR . "$name.yml", $str) !== false;
    }

    public function write(string $name, bool $staticResource = false): bool
    {
        $str = Yaml::dump(
            $this->getArrayCopy(),
            6,
            flags: Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_NULL_AS_TILDE
        );
        return file_put_contents(Globals::STATIC_DIR . "$name.yml", $str) !== false;
    }

}
