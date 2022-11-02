<?php

namespace Thor\Database\Definition;

class PrimaryKey extends Index
{

    /**
     * @param Column[] $columns
     */
    public function __construct(array $columns)
    {
        parent::__construct('PRIMARY', $columns, true);
    }

}
