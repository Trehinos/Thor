<?php

namespace Thor\Cli\Daemon;

use Countable;

interface AutomatonInterface extends DaemonInterface, Countable
{

    public function isLoaded(): bool;

    /**
     * Before load()
     *
     * @return void
     */
    public function init(): void;

    /**
     * After load() and before the loop.
     *
     * @return void
     */
    public function before(): void;

    /**
     * After the loop.
     *
     * @return void
     */
    public function after(): void;

    /**
     * @return array
     */
    public function load(): array;

    /**
     * @param string|int $index
     * @param mixed      $item
     *
     * @return bool
     */
    public function one(string|int $index, mixed $item): bool;

}
