<?php

namespace Thor\Api\Entities;

use Thor\Database\PdoTable\AdvancedPdoRow;
use Thor\Database\PdoTable\Attributes\PdoColumn;
use Thor\Database\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\Attributes\PdoRow;
use Thor\Database\PdoTable\PdoRowInterface;

#[PdoRow("group", ['id'], 'id')]
#[PdoColumn('id', 'INTEGER', 'integer', false)]
#[PdoColumn('group_name', 'INTEGER', 'integer', true)]
#[PdoIndex(['groupName', true])]
class Group implements PdoRowInterface
{

    use AdvancedPdoRow {
        AdvancedPdoRow::__construct as private adwConstruct;
    }

    public function __construct(?string $public_id = null, array $primaries = [])
    {
        $this->adwConstruct($public_id, $primaries);
    }

}
