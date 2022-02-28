<?php

namespace Thor\Database\PdoTable\TableType;

class BooleanType extends BaseType
{

    public function __construct(
        string $sqlType = 'INTEGER(1)',
        private readonly string $sqlTrue = '1',
        private readonly string $sqlFalse = '0'
    ) {
        parent::__construct($sqlType, 'bool');
    }

    public function toPhpValue(mixed $sqlValue): bool
    {
        return $sqlValue === $this->sqlTrue;
    }

    public function toSqlValue(mixed $phpValue): string
    {
        return $phpValue ? $this->sqlTrue : $this->sqlFalse;
    }

}
