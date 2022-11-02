<?php

namespace Thor\Database\Definition\TableType;

/**
 *
 */

/**
 *
 */
abstract class BaseType implements TypeInterface
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
    final public function phpType(): string
    {
        return $this->phpType;
    }

    /**
     * @return string
     */
    final public function sqlType(): string
    {
        return $this->sqlType;
    }

}
