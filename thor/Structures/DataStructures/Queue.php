<?php

namespace Thor\Structures\DataStructures;

interface Queue
{

    public function dequeue(): mixed;

    public function queue(mixed $value): void;

}
