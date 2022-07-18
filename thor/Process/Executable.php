<?php

namespace Thor\Process;

/**
 * This interface describes a process that can be executed.
 *
 * @package Thor
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
interface Executable
{

    /**
     * Executes the process.
     *
     * @return void
     */
    public function execute(): void;

}
