<?php

namespace Thor\Database\Definition\TableType;

/**
 *
 */

/**
 *
 */
class ArrayType extends BaseType
{

    /**
     * @param int    $sqlStringSize
     * @param string $sqlStringType
     */
    public function __construct(
        int $sqlStringSize = 4096,
        string $sqlStringType = 'VARCHAR'
    ) {
        parent::__construct("$sqlStringType($sqlStringSize)", 'int');
    }

    /**
     * @param mixed $sqlValue
     *
     * @return array
     */
    public function toPhpValue(mixed $sqlValue): array
    {
        return json_decode($sqlValue, true);
    }

    /**
     * @param mixed $phpValue
     *
     * @return string
     */
    public function toSqlValue(mixed $phpValue): string
    {
        return json_encode($phpValue);
    }
}
