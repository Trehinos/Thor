<?php

namespace Tests;

use Thor\Database\PdoTable\BasePdoRow;
use Thor\Database\PdoTable\Attributes\PdoTable;
use Thor\Database\PdoTable\Attributes\PdoColumn;

#[PdoTable('test', ['id'])]
#[PdoColumn('id', 'INTEGER', 'int', nullable: false)]
#[PdoColumn('data', 'VARCHAR(255)', 'string')]
final class TestTable extends BasePdoRow
{

    public function __construct(?int $id = null, public ?string $data = null)
    {
        parent::__construct(['id' => $id]);
    }

}
