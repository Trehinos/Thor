<?php

namespace Thor\Framework\Configurations;

use Thor\Configuration\ConfigurationFromFile;

/**
 *
 */

/**
 *
 */
final class LanguageDictionary extends ConfigurationFromFile
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
     * @param string $lang
     */
    public function __construct(string $lang)
    {
        parent::__construct("langs/$lang", true);
    }

}
