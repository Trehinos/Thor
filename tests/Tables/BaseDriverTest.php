<?php

namespace Tests\Tables;

use Thor\Database\PdoTable\PdoTable\BasePdoRow;
use Thor\Database\PdoTable\TableType\StringType;
use Thor\Database\PdoTable\TableType\IntegerType;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoTable;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoColumn;

#[PdoTable('base_driver_test', ['id'], 'id')]
#[PdoColumn('id', new IntegerType(), false)]
#[PdoColumn('name', new StringType(), false)]
#[PdoColumn('description', new StringType(), false, '')]
#[PdoIndex(['name', true])]
final class BaseDriverTest extends BasePdoRow
{

    public function __construct(public string $name, public string $description = '')
    {
        parent::__construct(['id' => null]);
    }

    public function getId(): ?int {
        return $this->primaries['id'] ?? null;
    }

}
