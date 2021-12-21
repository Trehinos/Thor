<?php

namespace Thor\Database\PdoTable\TableType;

class StringType implements TableTypeInterface
{

    public function __construct(private int $size = 255, private string $sqlType = 'VARCHAR')
    {
    }

    public function phpType(): string
    {
        return 'string';
    }

    public function sqlType(): string
    {
        return "{$this->sqlType}({$this->size})";
    }

    public function toPhpValue(mixed $sqlValue): string
    {
        return $sqlValue;
    }

    public function toSqlValue(mixed $phpValue): string
    {
        return (string)$phpValue;
    }
}
