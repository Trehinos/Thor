<?php

namespace Thor\Database\PdoTable\TableType;

interface TableTypeInterface
{

    public function phpType(): string;
    public function sqlType(): string;
    public function toPhpValue(mixed $sqlValue): mixed;
    public function toSqlValue(mixed $phpValue): mixed;

}
