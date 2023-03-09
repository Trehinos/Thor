<?php

namespace JuNe\D2;

use Evolution\june\Process\Node;
use Evolution\june\Process\Priority;

class Node2D extends Node
{

    public float $x = 0;
    public float $y = 0;

    public function __construct(string $name, string $description = '', Priority $priority = Priority::AVG)
    {
        parent::__construct($name, $description, $priority);
    }

    public function draw(): void
    {
    }

}
