<?php

namespace Thor;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Yaml\Yaml;

final class Thor
{

    public const VERSION = '0.3-dev';
    public const VERSION_NAME = 'gamma';
    public const DEFAULT_LANGUAGE = 'fr';

    private static ?self $defaultInstance = null;

    public function __construct(private array $configurations = [])
    {
    }

    #[ArrayShape([
        'config' => "array|mixed",
        'database' => "array|mixed",
        'routes' => "array|mixed",
        'security' => "array|mixed",
        'twig' => "array|mixed",
        'language' => "array|mixed"
    ])] public function getHttpConfiguration(): array
    {
        return $this->getConfigurationSet(['config', 'database', 'security', 'twig'], ['routes']) + [
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
        $language = self::DEFAULT_LANGUAGE;

        return self::$defaultInstance ??= new self();
    }

}
