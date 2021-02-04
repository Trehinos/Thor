<?php

namespace Thor\Database\PdoExtension;

use Attribute;

#[Attribute]
class PdoRow
{

    public function __construct(
        private string $tableName,
        private array $indexes = []
    )
    {}

    public function getTableName(): string
    {
        return $this->tableName;
    }

}
