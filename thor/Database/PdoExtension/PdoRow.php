<?php

namespace Thor\Database\PdoExtension;

use Attribute;
use JetBrains\PhpStorm\ArrayShape;

#[Attribute(Attribute::TARGET_CLASS)]
class PdoRow
{

    public function __construct(
        private string $tableName,
        #[ArrayShape(['primary' => '?array', 'auto' => '?string'])]
        private array $indexes = []
    ) {
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    #[ArrayShape(['primary' => '?array', 'auto' => '?string'])]
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    public function getPrimaryKeys(): array
    {
        return $this->indexes['primary'] ?? [];
    }

    public function getAutoColumnName(): ?string
    {
        return $this->indexes['auto'] ?? null;
    }

}
