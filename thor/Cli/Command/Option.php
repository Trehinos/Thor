<?php

namespace Thor\Cli\Command;

class Option
{

    public function __construct(
        public string $name,
        public string $description,
        public ?string $short = null,
        public ?string $long = null,
        public bool $hasValue = true,
        public bool $cumulative = false
    ) {
        $this->short ??= substr($this->name, 0, 1);
        $this->long ??= $this->name;
    }

    public function value(mixed $value = null): mixed
    {
        if (!$this->hasValue) {
            return $value != false;
        } else {
            if ($this->cumulative) {
                return $value + 1;
            } else {
                return $value;
            }
        }
    }

    public static function fromArray(array $argument): self
    {
        return new self(
            $argument['name'],
            $argument['description'] ?? '',
            $argument['short'] ?? null,
            $argument['long'] ?? null,
            $argument['hasValue'] ?? true,
            $argument['cumulative'] ?? false,
        );
    }

    /**
     * @return static[]
     */
    public static function fromConfiguration(array $configuration): array
    {
        $array = [];
        foreach ($configuration as $optName => $optSpecs) {
            $array[] = self::fromArray(['name' => $optName, ...$optSpecs]);
        }

        return $array;
    }

}
