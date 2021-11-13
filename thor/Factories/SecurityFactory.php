<?php

namespace Thor\Factories;

use Thor\Security\Security;

final class SecurityFactory extends Factory
{

    public function __construct(private array $configuration) {

    }

    public function produce(array $options = []): Security
    {
        return Security::createFromConfiguration($this->configuration);
    }
}
