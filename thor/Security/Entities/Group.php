<?php

namespace Thor\Security\Entities;

use Thor\Database\PdoExtension\AdvancedPdoRow;
use Thor\Database\PdoExtension\Attributes\PdoColumn;
use Thor\Database\PdoExtension\Attributes\PdoIndex;
use Thor\Database\PdoExtension\Attributes\PdoRow;
use Thor\Database\PdoExtension\PdoRowInterface;

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