<?php

namespace Thor\Structures;

interface ItemInterface
{

    public function getKey(): string;
    public function getValue(): mixed;

}
