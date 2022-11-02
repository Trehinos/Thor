<?php

namespace Thor\Database\Definition;

use Thor\Database\Definition\TableType\TypeInterface;

class Column
{

    public function __construct(
        public readonly string        $name,
        public readonly TypeInterface $type,
        public readonly mixed         $defaultValue = null,
        public readonly bool          $isNullable = true,
        public readonly bool          $autoIncremented = false,
    ) {
    }

    public function getSqlValue(mixed $fromPhpValue): mixed
    {
        return $this->type->toSqlValue($fromPhpValue);
    }

    public function getPhpValue(mixed $fromSqlValue): mixed
    {
        return $this->type->toSqlValue($fromSqlValue);
    }

}
