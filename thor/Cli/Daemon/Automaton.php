<?php

namespace Thor\Cli\Daemon;

abstract class Automaton extends Daemon implements AutomatonInterface
{

    private ?int $length = null;

    final public function count(): int
    {
        return $this->length ?? 0;
    }

    /**
     * Returns true AFTER load() has been called by execute().
     *
     * @return bool
     */
    final public function isLoaded(): bool
    {
        return $this->length !== null;
    }

    /**
     * Executes :
     * - init()
     * - elements = load()
     * - before()
     * - for each elements as index : element
     *      - one(index, element)
     * - after()
     *
     * @return void
     */
    final public function execute(): void
    {
        $this->init();
        $collection = $this->load();
        $this->length = count($collection);
        $this->before();
        foreach ($collection as $index => $item) {
            if (!$this->one($index, $item)) {
                break;
            }
        }
        $this->after();
    }

    /**
     * Before load()
     *
     * @return void
     */
    public function init(): void
    {
    }

    /**
     * After load() and before the loop.
     *
     * @return void
     */
    public function before(): void
    {
    }

    /**
     * After the loop.
     *
     * @return void
     */
    public function after(): void
    {
    }

}
