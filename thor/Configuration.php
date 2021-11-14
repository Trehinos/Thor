<?php

/**
 * @package Trehinos/Thor
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Yaml\Yaml;

final class Configuration
{

    private static ?self $defaultInstance = null;

    public function __construct(private array $configurations = [])
    {
    }

    #[ArrayShape([
        'config' => "array|mixed",
        'database' => "array|mixed",
        'api-routes' => "array|mixed",
        'security' => "array|mixed",
        'language' => "array|mixed"
    ])] public function getHttpConfiguration(): array
    {
        return $this->getConfigurationSet(['config', 'database', 'security'], ['api-routes']) + [
                'language' => Yaml::parseFile(
                    Globals::STATIC_DIR . "langs/{$this->configurations['config']['lang']}.yml"
                )
            ];
    }

    #[ArrayShape([
        'config' => "array|mixed",
        'database' => "array|mixed",
        'web-routes' => "array|mixed",
        'security' => "array|mixed",
        'twig' => "array|mixed",
        'language' => "array|mixed"
    ])] public function getWebConfiguration(): array
    {
        return $this->getConfigurationSet(['config', 'database', 'security', 'twig'], ['web-routes']) + [
                'language' => Yaml::parseFile(
                    Globals::STATIC_DIR . "langs/{$this->configurations['config']['lang']}.yml"
                )
            ];
    }

    public function getConfigurationSet(array $configList, array $staticList): array
    {
        foreach (
            [
                ['isStatic' => false, 'list' => $configList],
                ['isStatic' => true, 'list' => $staticList],
            ]
            as ['isStatic' => $isStatic, 'list' => $list]
        ) {
            foreach ($list as $item) {
                $this->configurations[$item] = $this->loadConfig($item, $isStatic);
            }
        }

        return array_intersect_key(
            $this->configurations,
            array_combine(
                array_merge($configList, $staticList),
                array_merge($configList, $staticList)
            )
        );
    }

    #[ArrayShape([
        'config' => "array|mixed",
        'database' => "array|mixed",
        'commands' => "array|mixed",
        'language' => "array|mixed"
    ])] public function getConsoleConfiguration(): array
    {
        return $this->getConfigurationSet(['config', 'database'], ['commands']) + [
                'language' => Yaml::parseFile(
                    Globals::STATIC_DIR . "langs/{$this->configurations['config']['lang']}.yml"
                )
            ];
    }

    public static function getDaemonsConfig(?string $fileName = null): array
    {
        $files = glob(Globals::STATIC_DIR . 'daemons/*.yml');
        $config = [];
        foreach ($files as $file) {
            if ($fileName && $file === $fileName) {
                $config[] = Yaml::parseFile($fileName);
            } elseif (null === $fileName) {
                $config[] = Yaml::parseFile($file);
            }
        }
        return $config;
    }

    public function loadConfig(string $configName, bool $typeStatic = false): array
    {
        return $this->configurations[$configName] ??=
            Yaml::parseFile(($typeStatic ? Globals::STATIC_DIR : Globals::CONFIG_DIR) . "$configName.yml");
    }

    public function loadStatic(string $configName): array
    {
        return $this->loadConfig($configName, true);
    }

    public static function getInstance(): self
    {
        return self::$defaultInstance ??= new self();
    }

}
