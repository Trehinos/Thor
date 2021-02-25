<?php

namespace Thor;

interface KernelInterface extends Executable
{

    public static function createFromConfiguration(array $config = []): static;
    public static function create(): static;

}
