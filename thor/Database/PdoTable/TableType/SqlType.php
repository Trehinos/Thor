<?php

namespace Thor\Database\PdoTable\TableType;

/**
 *
 */

/**
 *
 */
class SqlType extends BaseType
{

    /**
     * @param string $sqlType
     */
    public function __construct(string $sqlType = 'VARCHAR')
    {
        parent::__construct("$sqlType", 'string');
    }

    /**
     * @param mixed $sqlValue
     *
     * @return string
     */
    public function toPhpValue(mixed $sqlValue): string
    {
        return "$sqlValue";
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
