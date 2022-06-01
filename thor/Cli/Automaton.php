<?php

namespace Thor\Cli;

/**
 *
 */

/**
 *
 */
abstract class Automaton extends Daemon
{

    /**
     * @return void
     */
    final public function execute(): void
    {
        $this->init();
        $collection = $this->load();
        $this->before();
        foreach ($collection as $item) {
            if (!$this->one($item)) {
                break;
            }
        }
        $this->after();
    }

    /**
     * @return void
     */
    public function init(): void
    {
    }

    /**
     * @return void
     */
    public function before(): void
    {
    }

    /**
     * @return void
     */
    public function after(): void
    {
    }

    /**
     * @return array
     */
    abstract public function load(): array;

    /**
     * @param mixed $item
     *
     * @return bool
     */
    abstract public function one(mixed $item): bool;

}
