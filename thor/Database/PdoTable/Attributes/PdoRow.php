<?php

/**
 * @package Trehinos/Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoTable\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class PdoRow
{

    public function __construct(
        private ?string $tableName = null,
        private array $primary = [],
        private ?string $auto = null
    ) {
    }

    public function getTableName(): ?string
    {
        return $this->tableName;
    }

    public function getPrimaryKeys(): array
    {
        return $this->primary;
    }

    public function getAutoColumnName(): ?string
    {
        return $this->auto;
    }

}
