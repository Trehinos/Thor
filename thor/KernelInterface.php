<?php

/**
 * @package Trehinos/Thor
 * @copyright (2021) Sébastien GELDREICH
 * @license MIT
 */

namespace Thor;

interface KernelInterface extends Executable
{

    public static function createFromConfiguration(array $config = []): static;
    public static function create(): static;

}
