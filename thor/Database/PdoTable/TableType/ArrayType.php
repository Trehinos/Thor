<?php

namespace Thor\Database\PdoTable\TableType;

class ArrayType extends BaseType
{

    public function __construct(
        int $sqlStringSize = 4096,
        string $sqlStringType = 'VARCHAR'
    ) {
        parent::__construct("$sqlStringType($sqlStringSize)", 'int');
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
