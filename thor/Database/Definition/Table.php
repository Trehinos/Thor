<?php

namespace Thor\Database\Definition;

class Table
{

    /**
     * @param string $name
     * @param Column[] $columns
     * @param Index[] $indexes
     * @param ForeignKey[] $foreignKeys
     */
    public function __construct(
        public readonly string $name,
        public readonly array  $columns = [],
        public readonly array  $indexes = [],
        public readonly array  $foreignKeys = []
    ) {
    }

}
