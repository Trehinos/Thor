<?php

namespace Thor\Process;

use Thor\Configuration\Configuration;

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

    public static function fromArray(array $argument): self
    {
        return new self($argument['name'], $argument['description'] ?? '', $argument['required'] ?? false);
    }

    /**
     * @return static[]
     */
    public static function fromConfiguration(array $configuration): array
    {
        $array = [];
        foreach ($configuration as $argName => $argSpecs) {
            $array[] = self::fromArray(['name' => $argName, ...$argSpecs]);
        }

        return $array;
    }

}
