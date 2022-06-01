<?php

namespace Thor\Database\PdoTable\TableType;

/**
 *
 */

/**
 *
 */
class StringType extends BaseType
{

    /**
     * @param int    $size
     * @param string $sqlType
     */
    public function __construct(public readonly int $size = 255, string $sqlType = 'VARCHAR')
    {
        parent::__construct("$sqlType({$this->size})", 'string');
    }

    /**
     * @param mixed $sqlValue
     *
     * @return string
     */
    public function toPhpValue(mixed $sqlValue): string
    {
        return $sqlValue;
    }

    /**
     * @param mixed $phpValue
     *
     * @return string
     */
    public function toSqlValue(mixed $phpValue): string
    {
        return "$phpValue";
    }
}
