<?php

/**
 * @package Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Api\Entities;

use Thor\Database\PdoTable\PdoRowTrait;
use Thor\Database\PdoTable\Attributes\PdoColumn;
use Thor\Database\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\Attributes\PdoRow;
use Thor\Database\PdoTable\PdoRowInterface;
use Thor\Database\PdoTable\Attributes\PdoForeignKey;

#[PdoRow("group_user", ['group_id', 'user_id'])]
#[PdoColumn('group_id', 'INTEGER', 'integer', false)]
#[PdoColumn('user_id', 'INTEGER', 'integer', false)]
#[PdoForeignKey(Group::class, ['id'], ['group_id'])]
#[PdoForeignKey(User::class, ['id'], ['user_id'])]
class GroupUser implements PdoRowInterface
{

    use PdoRowTrait {
        PdoRowTrait::__construct as private adwConstruct;
    }

    public function __construct(?string $public_id = null, array $primaries = [])
    {
        $this->adwConstruct($public_id, $primaries);
    }

}
