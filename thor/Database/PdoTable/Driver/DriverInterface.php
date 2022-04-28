<?php

namespace Thor\Database\PdoTable\Driver;

use Thor\Database\PdoTable\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoTable;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoColumn;

interface DriverInterface
{

    public function addColumn(PdoColumn $column, ?string $autoKey = null): string;

    public function addIndex(PdoIndex $index): string;

    public function primaryKeys(PdoTable $table, ?string $autoKey = null): string;

    public function createTable(string $className): string;

    /**
     * @return string[]
     */
    public function createIndexes(string $className): array;

}
