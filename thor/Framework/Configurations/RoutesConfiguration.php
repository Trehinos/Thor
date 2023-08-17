<?php

namespace Thor\Framework\Configurations;

use Thor\Configuration\ConfigurationFromResource;

final class RoutesConfiguration extends ConfigurationFromResource
{

    private static array $configurations = [];

    /**
     * @param mixed ...$args
     *
     * @return static
     */
    public static function get(mixed ...$args): static
    {
        return static::$configurations[static::class][$args[0]] ??= new static(...$args);
    }

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        parent::__construct("$type-routes");
    }

}
