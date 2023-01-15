<?php

namespace Evolution\DataModel\Resource;

class Resource
{

    public function __construct(
        public string $name,
        public bool $primary = true,
        public string $icon = 'square',
        public string $color = '#fff',
    ) {}

}
