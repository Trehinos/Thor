<?php

namespace Thor\Configuration;

use Thor\Tools\Strings;

final readonly class ConfigurationFolder
{

    public function __construct(private array $context) {}

    public function getPath(string $configLine): string
    {
        return Strings::interpolate($configLine, $this->context);
    }

}
