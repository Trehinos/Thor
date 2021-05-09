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

#[PdoRow("group", ['id'], 'id')]
#[PdoColumn('id', 'INTEGER', 'integer', false)]
#[PdoColumn('group_name', 'INTEGER', 'integer', true)]
#[PdoIndex(['groupName', true])]
class Group implements PdoRowInterface
{

    use PdoRowTrait {
        PdoRowTrait::__construct as private adwConstruct;
    }

    public function __construct(?string $public_id = null, array $primaries = [])
    {
        $this->adwConstruct($public_id, $primaries);
    }

}
