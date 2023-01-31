<?php

namespace Evolution\DataModel\Building;

use Thor\Database\PdoTable\PdoTable\Attributes\PdoColumn;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoTable;
use Thor\Database\PdoTable\TableType\ArrayType;
use Thor\Database\PdoTable\TableType\IntegerType;
use Thor\Database\PdoTable\TableType\JsonType;
use Thor\Database\PdoTable\TableType\StringType;

#[PdoTable('building', ['id'], 'id')]
#[PdoIndex(['name'], true)]
#[PdoColumn('id', new IntegerType(), false)]
#[PdoColumn('name', new StringType(), false)]
#[PdoColumn('buildTime', new IntegerType(), false)]
#[PdoColumn('costs', new JsonType(), false, [])]
#[PdoColumn('sizeX', new IntegerType(), false, 1)]
#[PdoColumn('sizeY', new IntegerType(), false, 1)]
#[PdoColumn('maxWorkers', new IntegerType(), false, 0)]
#[PdoColumn('recipes', new ArrayType(), false, [])]
class Building
{

    public function __construct(
        public string $name,
        public int    $buildTime,
        public array  $costs = [],
        public int    $sizeX = 1,
        public int    $sizeY = 1,
        public int    $maxWorkers = 0,
        public array  $recipes = []
    ) {
    }

}
