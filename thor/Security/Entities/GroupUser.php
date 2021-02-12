<?php

namespace Thor\Security\Entities;

use Thor\Database\PdoExtension\AdvancedPdoRow;
use Thor\Database\PdoExtension\Attributes\PdoColumn;
use Thor\Database\PdoExtension\Attributes\PdoForeignKey;
use Thor\Database\PdoExtension\Attributes\PdoRow;
use Thor\Database\PdoExtension\PdoRowInterface;

#[PdoRow("group_user", ['group_id', 'user_id'])]
#[PdoColumn('group_id', 'INTEGER', 'integer', false)]
#[PdoColumn('user_id', 'INTEGER', 'integer', false)]
#[PdoForeignKey(Group::class, ['id'], ['group_id'])]
#[PdoForeignKey(User::class, ['id'], ['user_id'])]
class GroupUser implements PdoRowInterface
{

    use AdvancedPdoRow {
        AdvancedPdoRow::__construct as private adwConstruct;
    }

    public function __construct(?string $public_id = null, array $primaries = [])
    {
        $this->adwConstruct($public_id, $primaries);
    }

}
