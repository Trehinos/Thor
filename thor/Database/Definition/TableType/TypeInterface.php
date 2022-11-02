<?php

namespace Thor\Database\Definition\TableType;

/**
 *
 */

/**
 *
 */
interface TypeInterface
{

    /**
     * @return string
     */
    public function phpType(): string;

    /**
     * @return string
     */
    public function sqlType(): string;

    /**
     * @param mixed $sqlValue
     *
     * @return mixed
     */
    public function toPhpValue(mixed $sqlValue): mixed;

    /**
     * @param mixed $phpValue
     *
     * @return mixed
     */
    public function toSqlValue(mixed $phpValue): mixed;

}
