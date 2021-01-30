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

    private array $configurations = [];

    public function __construct(
        private ?string $dbDefinitionFile = null,
        private ?string $databaseConfigFile = null,
        private ?string $menuFile = null,
        private ?string $routesFile = null,
        private ?string $commandsFile = null,
        private ?string $languageFile = null,
        private ?string $twigFile = null,
        private ?string $securityFile = null
    ) {
    }

    public function getDefinitionHelperConfiguration(): array
    {
        return $this->loadConfig($this->dbDefinitionFile);
    }

    #[ArrayShape([
        'databases' => "array|mixed",
        'routes' => "array|mixed",
        'security' => "array|mixed",
        'twig' => "array|mixed",
        'language' => "array|mixed"
    ])] public function getHttpConfiguration(): array
    {
        return [
            'databases' => $this->loadConfig($this->databaseConfigFile),
            'routes' => $this->loadConfig($this->routesFile),
            'security' => $this->loadConfig($this->securityFile),
            'twig' => $this->loadConfig($this->twigFile),
            'language' => $this->loadConfig($this->languageFile)
        ];
    }

    #[ArrayShape([
        'databases' => "array|mixed",
        'commands' => "array|mixed",
        'language' => "array|mixed"
    ])] public function getConsoleConfiguration(): array
    {
        $consoleConfiguration = [
            'databases' => $this->databaseConfigFile ? Yaml::parseFile($this->databaseConfigFile) : [],
            'commands' => $this->commandsFile ? Yaml::parseFile($this->commandsFile) : [],
            'language' => $this->languageFile ? Yaml::parseFile($this->languageFile) : []
        ];

        $this->configurations = $consoleConfiguration + $this->configurations;

        return $consoleConfiguration;
    }

    private function loadConfig(?string $configName): array
    {
        return $this->configurations[$configName] ??= ($configName ? Yaml::parseFile($configName) : []);
    }

    public static function getInstance(): self
    {
        $language = self::DEFAULT_LANGUAGE;

        return self::$defaultInstance ??= new self(
            Globals::STATIC_DIR . 'db_definition.yml',
            Globals::CONFIG_DIR . 'database.yml',
            Globals::STATIC_DIR . 'menu.yml',
            Globals::STATIC_DIR . 'routes.yml',
            Globals::STATIC_DIR . 'commands.yml',
            Globals::STATIC_DIR . "langs/$language.yml",
            Globals::CONFIG_DIR . 'twig.yml',
            Globals::CONFIG_DIR . 'security.yml'
        );
    }

}
