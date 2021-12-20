<?php

namespace Thor\Database\PdoTable\TableType;

class ArrayType implements TableTypeInterface
{

    public function __construct(private int $sqlStringSize = 4096, private string $sqlType = 'VARCHAR')
    {
    }

    public function phpType(): string
    {
        return 'array';
    }

    public function sqlType(): string
    {
        return "{$this->sqlType}({$this->sqlStringSize}";
    }

    public function toPhpValue(mixed $sqlValue): array
    {
        return json_decode($sqlValue, true);
    }

    public function toSqlValue(mixed $phpValue): string
    {
        return json_encode($phpValue);
    }
}
