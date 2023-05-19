<?php

namespace Thor\Tools\Structures\DataStructures;

/**
 *
 */

/**
 *
 */
interface Stack
{

    /**
     * @return mixed
     */
    public function pop(): mixed;

    /**
     * @param mixed ...$value
     *
     * @return int
     */
    public function push(mixed ...$value): int;

}
