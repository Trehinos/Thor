<?php

namespace Thor\Database\PdoTable\TableType;

/**
 *
 */

/**
 *
 */
class JsonType extends BaseType
{

    /**
     * @param int $sqlStringSize
     * @param string $sqlStringType
     * @param bool $associative
     */
    public function __construct(
        int                   $sqlStringSize = 16384,
        string                $sqlStringType = 'VARCHAR',
        private readonly bool $associative = true,
    ) {
        parent::__construct("$sqlStringType($sqlStringSize)", 'int');
    }

    /**
     * @param string $sqlValue
     *
     * @return mixed
     */
    public function toPhpValue(mixed $sqlValue): mixed
    {
        return json_decode($sqlValue, $this->associative);
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
