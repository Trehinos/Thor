<?php

namespace Thor\Database\PdoTable\Driver;

use Thor\Database\PdoTable\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoTable;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoColumn;

/**
 *
 */

/**
 *
 */
interface DriverInterface
{

    /**
     * @param PdoColumn   $column
     * @param string|null $autoKey
     *
     * @return string
     */
    public function addColumn(PdoColumn $column, ?string $autoKey = null): string;

    /**
     * @param PdoIndex $index
     *
     * @return string
     */
    public function addIndex(PdoIndex $index): string;

    /**
     * @param PdoTable    $table
     * @param string|null $autoKey
     *
     * @return string
     */
    public function primaryKeys(PdoTable $table, ?string $autoKey = null): string;

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
