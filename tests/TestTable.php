<?php

namespace Tests;

use Thor\Database\PdoTable\PdoRow\Row;
use Thor\Database\PdoTable\TableType\StringType;
use Thor\Database\PdoTable\TableType\IntegerType;
use Thor\Database\PdoTable\PdoRow\Attributes\Table;
use Thor\Database\PdoTable\PdoRow\Attributes\Column;

#[Table('test', ['id'])]
#[Column('id', new IntegerType(), nullable: false)]
#[Column('data', new StringType())]
final class TestTable extends Row
{

    public function __construct(?int $id = null, public ?string $data = null)
    {
        parent::__construct(['id' => $id]);
    }

}
