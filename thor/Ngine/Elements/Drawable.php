<?php

namespace Thor\Ngine\Elements;

use Thor\Ngine\Node;

class Drawable extends Node
{

    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    final public function draw(): void
    {
        $this->onDraw();
        $this->each(fn(Drawable $child) => $child->draw(), Drawable::class);
        $this->onDrew();
    }

    public function onDraw(): void
    {
    }

    public function onDrew(): void
    {
    }

}
