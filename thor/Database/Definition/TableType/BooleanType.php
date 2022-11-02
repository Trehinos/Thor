<?php

namespace Thor\Database\Definition\TableType;

/**
 *
 */

/**
 *
 */
class BooleanType extends BaseType
{

    /**
     * @param string $sqlType
     * @param string $sqlTrue
     * @param string $sqlFalse
     */
    public function __construct(
        string $sqlType = 'INTEGER(1)',
        private readonly string $sqlTrue = '1',
        private readonly string $sqlFalse = '0'
    ) {
        parent::__construct($sqlType, 'bool');
    }

    /**
     * @param mixed $sqlValue
     *
     * @return bool
     */
    public function toPhpValue(mixed $sqlValue): bool
    {
        return $sqlValue === $this->sqlTrue;
    }

    /**
     * @param mixed $phpValue
     *
     * @return string
     */
    public function toSqlValue(mixed $phpValue): string
    {
        return $phpValue ? $this->sqlTrue : $this->sqlFalse;
    }

}
