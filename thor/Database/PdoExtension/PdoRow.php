<?php

namespace Thor\Database\PdoExtension;

use Attribute;

#[Attribute]
class PdoRow implements PdoRowInterface
{

    use AdvancedPdoRow;

    public function __construct(
        private string $tableName
    )
    {}

    public function getTableName(): string
    {
        return $this->tableName;
    }

}
