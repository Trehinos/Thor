<?php

namespace June\D2;

use June\Process\Node;

class Node2D extends Node
{

    public function __construct(
        string $name,
        public float $x = 0,
        public float $y = 0,
        public float $rotation = 0,
        public float $scale_x = 1,
        public float $scale_y = 1
    ) {
        parent::__construct($name);
    }

    public function getRepr(): string
    {
        $this->rotation %= 360;
        return "(x=$this->x, y=$this->y, angle={$this->rotation}°, scale=$this->scale_x/$this->scale_y)";
    }

}
