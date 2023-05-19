<?php

namespace Thor\Tools\Structures\DataStructures;

/**
 *
 */

/**
 *
 */
interface ListInterface extends Queue, Iterator, Stack
{

    /**
     * @return mixed
     */
    public function penultimate(): mixed;

    /**
     * @return mixed
     */
    public function second(): mixed;

    /**
     * @return mixed
     */
    public function first(): mixed;

    /**
     * @return mixed
     */
    public function last(): mixed;

}
