<?php

namespace Thor\Factories;

abstract class Factory
{
    abstract public function produce(array $options = []): mixed;
}
