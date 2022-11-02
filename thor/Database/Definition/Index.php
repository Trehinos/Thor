<?php

namespace Thor\Database\Definition;

class Index
{

    /**
     * @param Column[] $columns
     */
    public function __construct(
        public readonly string $name,
        public readonly array  $columns,
        public readonly bool   $isUnique = false
    ) {
    }

}
