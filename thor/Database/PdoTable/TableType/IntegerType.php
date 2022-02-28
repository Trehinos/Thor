<?php

namespace Thor\Database\PdoTable\TableType;

class IntegerType extends BaseType
{

    public function __construct(public readonly int $size = 10)
    {
        parent::__construct("INTEGER({$this->size})", 'int');
    }

    public function toPhpValue(mixed $sqlValue): int
    {
        return $sqlValue;
    }

    public function toSqlValue(mixed $phpValue): int
    {
        return (int)$phpValue;
    }

}
