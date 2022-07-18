<?php

namespace Thor\Process;

use Thor\Configuration\Configuration;

/**
 * Defines a Thor Kernel.
 *
 * @package          Thor
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
interface KernelInterface extends Executable
{

    /**
     * This static function MUST return a new KernelInterface with specified configuration.
     *
     * The implementation is responsible on how the $config array is used.
     *
     * @param Configuration $config
     *
     * @return static
     */
    public static function createFromConfiguration(Configuration $config): static;

    /**
     * This function return a new KernelInterface with default configuration.
     * It SHOULD load a configuration file and use it to instantiate the Kernel.
     *
     * @return static
     */
    public static function create(): static;

}
