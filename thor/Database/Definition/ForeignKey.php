<?php

namespace Thor\Database\Definition;

class ForeignKey
{

    /**
     * @param Column[] $hostColumns
     * @param Column[] $targetColumns
     */
    public function __construct(
        public readonly string $name,
        public readonly array  $hostColumns,
        public readonly Table  $targetTable,
        public readonly array  $targetColumns,
    ) {
    }

}
