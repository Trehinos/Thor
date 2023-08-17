<?php

namespace Ems\Packages\Sales\Element;

readonly class Unit {

    public function __construct(
        public string $name,
        public string $abbr,
    ) {
    }

}


