<?php

namespace Thor\Database\Definition\TableType;

class IntegerType extends BaseType
{

    /**
     * @param int $size
     */
    public function __construct(public readonly int $size = 10)
    {
        parent::__construct("INTEGER({$this->size})", 'int');
    }

    /**
     * @param mixed $sqlValue
     *
     * @return int
     */
    public function toPhpValue(mixed $sqlValue): int
    {
        return $sqlValue;
    }

    /**
     * @param mixed $phpValue
     *
     * @return int
     */
    public function toSqlValue(mixed $phpValue): int
    {
        return (int)$phpValue;
    }

}
