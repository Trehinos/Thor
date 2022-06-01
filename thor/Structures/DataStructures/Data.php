<?php

namespace Thor\Structures\DataStructures;

use Thor\Structures\Collection\Collection;

/**
 *
 */

/**
 *
 */
class Data extends Collection implements ListInterface
{

    /**
     * @return mixed
     */
    public function last(): mixed
    {
        return $this[parent::keyLast()] ?? null;
    }

    /**
     * @return mixed
     */
    public function first(): mixed
    {
        return $this[parent::keyFirst()] ?? null;
    }

    /**
     * @return mixed
     */
    public function dequeue(): mixed
    {
        if (($last = $this->toArray()[$this->keyLast()] ?? null ) === null) {
            unset($this[$this->keyLast()]);
        }

        return $last;
    }

    /**
     * @return mixed
     */
    public function second(): mixed
    {
        $keys = $this->keys();
        $keys->pop();
        return $this[$keys->first()] ?? null;
    }

    /**
     * @return mixed
     */
    public function penultimate(): mixed
    {
        $keys = $this->keys();
        $keys->dequeue();
        return $this[$keys->last()] ?? null;
    }

    /**
     * @param mixed           $value
     * @param string|int|null $key
     *
     * @return void
     */
    public function queue(mixed $value, string|int|null $key = null): void
    {
        if ($key !== null) {
            $this[] = $value;
        } else {
            $this[$key] = $value;
        }
    }

    /**
     * @return mixed
     */
    public function previous(): mixed
    {
        return prev($this->data) ?: null;
    }
}
