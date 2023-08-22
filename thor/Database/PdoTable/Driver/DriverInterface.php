<?php

namespace Thor\Database\PdoTable\Driver;

use Thor\Database\PdoTable\PdoRow\Attributes\Index;
use Thor\Database\PdoTable\PdoRow\Attributes\Table;
use Thor\Database\PdoTable\PdoRow\Attributes\Column;

/**
 *
 */

/**
 *
 */
interface DriverInterface
{

    /**
     * @param Column   $column
     * @param string|null $autoKey
     *
     * @return string
     */
    public function addColumn(Column $column, ?string $autoKey = null): string;

    /**
     * @param Index $index
     *
     * @return string
     */
    public function addIndex(Index $index): string;

    /**
     * @param Table    $table
     * @param string|null $autoKey
     *
     * @return string
     */
    public function primaryKeys(Table $table, ?string $autoKey = null): string;

    /**
     * @param string $className
     *
     * @return string
     */
    public function createTable(string $className): string;

    /**
     * @return string[]
     */
    public function createIndexes(string $className): array;

}
