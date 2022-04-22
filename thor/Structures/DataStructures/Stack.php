<?php

namespace Thor\Structures\DataStructures;

interface Stack
{

    public function pop(): mixed;

    public function push(mixed ...$value): int;

}
