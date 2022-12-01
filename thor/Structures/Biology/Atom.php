<?php

namespace Thor\Structures\Biology;

final class Atom
{

    public function __construct(
        public int $number,
        public string $label,
        public string $name,
    ) {}

}
