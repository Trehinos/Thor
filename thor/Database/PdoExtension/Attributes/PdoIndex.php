<?php

namespace Thor\Database\PdoExtension\Attributes;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class PdoIndex
{

    private string $name;

    public function __construct(
        private array $columnNames,
        ?string $name = null,
        private bool $isUnique = false
    ) {
        $this->name = $name ??
            (($this->isUnique ? 'uniq_' : 'index_') . implode('_', $this->columnNames));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumnNames(): array
    {
        return $this->columnNames;
    }

    public function isUnique(): bool
    {
        return $this->isUnique;
    }


}
