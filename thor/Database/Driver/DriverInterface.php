<?php

namespace Thor\Database\Driver;

use Thor\Database\Definition\Attributes\C;
use Thor\Database\Definition\Attributes\I;
use Thor\Database\Definition\Attributes\T;

interface DriverInterface
{

    /**
     * @param C   $column
     * @param string|null $autoKey
     *
     * @return string
     */
    public function addColumn(C $column, ?string $autoKey = null): string;

    /**
     * @param I $index
     *
     * @return string
     */
    public function addIndex(I $index): string;

    /**
     * @param T    $table
     * @param string|null $autoKey
     *
     * @return string
     */
    public function primaryKeys(T $table, ?string $autoKey = null): string;

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

    // TODO : CRUD

}
