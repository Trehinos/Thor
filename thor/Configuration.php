<?php

namespace Thor;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;
use Symfony\Component\Yaml\Yaml;

final class Configuration
{

    private static ?self $defaultInstance = null;

    public function __construct(private array $configurations = [])
    {
    }

    #[ExpectedValues(['dev', 'debug', 'verbose', 'prod'])]
    public function getEnv(): string
    {
        return $this->loadConfig('config')['env'] ?? 'dev';
    }

    public static function isDev(): bool
    {
        return self::getInstance()->getEnv() === 'dev';
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
        return self::$defaultInstance ??= new self();
    }

}
