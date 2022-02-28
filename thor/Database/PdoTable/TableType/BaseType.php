<?php

namespace Thor\Database\PdoTable\TableType;

abstract class BaseType implements TableTypeInterface
{

    public function __construct(
        protected readonly string $sqlType,
        protected readonly string $phpType,
    ) {
    }

    public function phpType(): string
    {
        return $this->phpType;
    }

    public function sqlType(): string
    {
        return $this->sqlType;
    }

    abstract public function toPhpValue(mixed $sqlValue): mixed;

    abstract public function toSqlValue(mixed $phpValue): mixed;

}
