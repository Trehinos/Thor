<?php

namespace Thor\Process;

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

}
