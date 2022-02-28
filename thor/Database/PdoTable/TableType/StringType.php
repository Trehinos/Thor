<?php

namespace Thor\Database\PdoTable\TableType;

class StringType extends BaseType
{

    public function __construct(public readonly int $size = 255, string $sqlType = 'VARCHAR')
    {
        parent::__construct("$sqlType({$this->size})", 'string');
    }

    public function toPhpValue(mixed $sqlValue): string
    {
        return $sqlValue;
    }

    public function toSqlValue(mixed $phpValue): string
    {
        return "$phpValue";
    }
}
