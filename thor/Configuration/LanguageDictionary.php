<?php

namespace Thor\Configuration;

final class LanguageDictionary extends ConfigurationFromFile
{

    private static array $configurations = [];

    public static function get(mixed ...$args): static
    {
        return static::$configurations[static::class][$args[0]] ??= new static(...$args);
    }

    public function __construct(string $lang)
    {
        parent::__construct("langs/$lang", true);
    }

}