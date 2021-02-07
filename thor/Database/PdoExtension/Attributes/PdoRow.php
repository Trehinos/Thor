<?php

namespace Thor\Database\PdoExtension\Attributes;

use Attribute;
use JetBrains\PhpStorm\ArrayShape;

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
