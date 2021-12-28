<?php

namespace Thor\Configuration;

use ArrayObject;

class Configuration extends ArrayObject
{

    private static ?Configuration $configuration = null;

    public function __construct(array $configArray = [])
    {
        parent::__construct($configArray, ArrayObject::ARRAY_AS_PROPS);
    }

    public function merge(Configuration $configuration): static
    {
        foreach ($configuration as $key => $value) {
            $this[$key] = $value;
        }
        return $this;
    }

    public static function get(mixed ...$args): static
    {
        return static::$configuration ??= new static(...$args);
    }

}
