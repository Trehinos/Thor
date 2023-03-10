<?php

namespace June\Process;
class Engine extends Node {

    public function __construct(
        public string $name,
        public string $description = '',
        public Priority $priority = Priority::AVG
    ) {
        parent::__construct($name, $this->description, $this->priority);
    }

    public function complete(float $delta): void
    {
        $this->init();
    }

}
