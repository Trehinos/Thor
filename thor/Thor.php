<?php

namespace Thor;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;
use Symfony\Component\Yaml\Yaml;

final class Thor
{

    public const VERSION = '0.4-dev';
    public const VERSION_NAME = 'δ';

    private function __construct()
    {
    }

    private static ?Configuration $configuration = null;

    public static function getConfiguration(): Configuration
    {
        return self::$configuration ??= Configuration::getInstance();
    }

    public static function config(string $name, bool $staticResource = false): array
    {
        return self::getConfiguration()->loadConfig($name, $staticResource);
    }


}
