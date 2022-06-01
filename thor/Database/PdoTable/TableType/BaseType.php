<?php

namespace Thor\Database\PdoTable\TableType;

/**
 *
 */

/**
 *
 */
abstract class BaseType implements TableTypeInterface
{

    /**
     * @param string $sqlType
     * @param string $phpType
     */
    public function __construct(
        protected readonly string $sqlType,
        protected readonly string $phpType,
    ) {
    }

    /**
     * @return string
     */
    public function phpType(): string
    {
        return $this->phpType;
    }

    /**
     * @return string
     */
    public function sqlType(): string
    {
        return $this->sqlType;
    }

    /**
     * @param mixed $sqlValue
     *
     * @return mixed
     */
    abstract public function toPhpValue(mixed $sqlValue): mixed;

    /**
     * @param mixed $phpValue
     *
     * @return mixed
     */
    abstract public function toSqlValue(mixed $phpValue): mixed;

}
