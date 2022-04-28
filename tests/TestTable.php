<?php

namespace Tests;

use Thor\Database\PdoTable\PdoTable\BasePdoRow;
use Thor\Database\PdoTable\TableType\StringType;
use Thor\Database\PdoTable\TableType\IntegerType;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoTable;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoColumn;

#[PdoTable('test', ['id'])]
#[PdoColumn('id', new IntegerType(), nullable: false)]
#[PdoColumn('data', new StringType())]
final class TestTable extends BasePdoRow
{

    public function __construct(?int $id = null, public ?string $data = null)
    {
        parent::__construct(['id' => $id]);
    }

}
