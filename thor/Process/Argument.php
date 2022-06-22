<?php

namespace Thor\Process;

class Argument
{

    /**
     * @var callable
     */
    private $validationFunction;

    public function __construct(
        public string $name,
        public string $description,
        public bool $required = false,
        ?callable $validate = null
    ) {
        $this->validationFunction = $validate ?? fn (string $argument) => true;
    }

}
