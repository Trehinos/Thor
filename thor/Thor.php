<?php

namespace Thor;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Yaml\Yaml;

final class Thor
{

    public const VERSION = '0.2';
    public const VERSION_NAME = 'beta';

    public function __construct(
        private ?string $dbDefinitionFile,
        private ?string $databaseConfigFile,
        private ?string $menuFile,
        private ?string $routesFile,
        private ?string $commandsFile,
        private ?string $languageFile,
        private ?string $twigFile,
        private ?string $securityFile
    ) {
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
            'databases' => $this->databaseConfigFile ? Yaml::parseFile($this->databaseConfigFile) : [],
            'routes' => $this->routesFile ? Yaml::parseFile($this->routesFile) : [],
            'security' => $this->securityFile ? Yaml::parseFile($this->securityFile) : [],
            'twig' => $this->twigFile ? Yaml::parseFile($this->twigFile) : [],
            'language' => $this->languageFile ? Yaml::parseFile($this->languageFile) : []
        ];
    }

    #[ArrayShape([
        'databases' => "array|mixed",
        'commands' => "array|mixed",
        'language' => "array|mixed"
    ])] public function getConsoleConfiguration(): array
    {
        return [
            'databases' => $this->databaseConfigFile ? Yaml::parseFile($this->databaseConfigFile) : [],
            'commands' => $this->commandsFile ? Yaml::parseFile($this->commandsFile) : [],
            'language' => $this->languageFile ? Yaml::parseFile($this->languageFile) : []
        ];
    }

}
