<?php

namespace Thor\Structures\DataStructures;

interface ListInterface extends Queue, Iterator, Stack
{

    public function penultimate(): mixed;

    public function second(): mixed;

    public function first(): mixed;

    public function last(): mixed;

}
