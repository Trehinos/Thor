<?php

namespace Thor\Structures\DataStructures;

/**
 *
 */

/**
 *
 */
interface Queue
{

    /**
     * @return mixed
     */
    public function dequeue(): mixed;

    /**
     * @param mixed $value
     *
     * @return void
     */
    public function queue(mixed $value): void;

}
