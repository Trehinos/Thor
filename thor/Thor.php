<?php

/**
 * @package Trehinos/Thor
 * @copyright (2021) Sébastien GELDREICH
 * @license MIT
 */

namespace Thor;

use JetBrains\PhpStorm\ExpectedValues;

final class Thor
{

    public const VERSION = '0.4';
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

    #[ExpectedValues(['dev', 'debug', 'verbose', 'prod'])]
    public static function getEnv(): string
    {
        return self::config('config')['env'] ?? 'dev';
    }

    public static function isDev(): bool
    {
        return self::getEnv() === 'dev';
    }



}
