<?php

namespace Thor;

use Symfony\Component\Yaml\Yaml;
use Thor\Debug\Logger;

final class ConfigurationLoader
{

    private array $files;

    /**
     * ConfigurationLoader constructor.
     *
     * @param array $files [key => filepath]
     */
    public function __construct(array $files)
    {
        $this->files = $files;
    }

    public function load(): array
    {
        $configArray = [];
        foreach ($this->files as $key => $filePath) {
            Logger::write("Load $key configuration");
            $configArray[$key] = Yaml::parseFile($filePath);
        }

        return $configArray;
    }

    /**
     * @param string ...$normalizedNames each 'name' matches Thor\Globals::CONFIG_DIR . 'name.yml'
     *
     * @return array
     */
    public static function loadConfig(string ...$normalizedNames): array
    {
        return (new self(
            array_combine(
                $normalizedNames,
                array_map(
                    fn(string $name) => Globals::CONFIG_DIR . "$name.yml",
                    $normalizedNames
                )
            )
        ))->load();
    }

    /**
     * @param string ...$normalizedNames each 'name' matches Thor\Globals::STATIC_DIR . 'name.yml'
     *
     * @return array
     */
    public static function loadStatic(string ...$normalizedNames): array
    {
        return (new self(
            array_combine(
                $normalizedNames,
                array_map(
                    fn(string $name) => Globals::STATIC_DIR . "$name.yml",
                    $normalizedNames
                )
            )
        ))->load();
    }

}
