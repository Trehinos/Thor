<?php

namespace Thor\Common\Configuration;

use Thor\Common\Types\Strings;

final readonly class ConfigurationFolder
{

    public function __construct(private array $context) {}

    public function getPath(string $configLine): string
    {
        return Strings::interpolate($configLine, $this->context);
    }

}
