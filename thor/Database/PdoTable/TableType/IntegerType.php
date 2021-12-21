<?php

namespace Thor\Database\PdoTable\TableType;

class IntegerType implements TableTypeInterface
{

    public function __construct(private int $size = 10)
    {
    }

    public function phpType(): string
    {
        return 'int';
    }

    public function sqlType(): string
    {
        return "INTEGER({$this->size})";
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
