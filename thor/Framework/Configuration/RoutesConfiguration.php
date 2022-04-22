<?php

namespace Thor\Framework\Configuration;

use Thor\Configuration\ConfigurationFromFile;

final class RoutesConfiguration extends ConfigurationFromFile
{

    private static array $configurations = [];

    public static function get(mixed ...$args): static
    {
        return static::$configurations[static::class][$args[0]] ??= new static(...$args);
    }

    public function __construct(string $type)
    {
        parent::__construct("$type-routes", true);
    }

}
