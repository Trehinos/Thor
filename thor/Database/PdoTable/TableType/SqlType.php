<?php

namespace Thor\Database\PdoTable\TableType;

class SqlType extends BaseType
{

    public function __construct(string $sqlType = 'VARCHAR')
    {
        parent::__construct("$sqlType", 'string');
    }

    public function toPhpValue(mixed $sqlValue): string
    {
        return "$sqlValue";
    }

    public function toSqlValue(mixed $phpValue): string
    {
        return "$phpValue";
    }

}
