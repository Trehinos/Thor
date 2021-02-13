<?php

namespace Thor\Database\PdoExtension\Attributes;

use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class PdoIndex
{

    private string $name;

    public function __construct(
        private array $columnNames,
        private bool $isUnique = false,
        ?string $name = null
    ) {
        $this->name = $name ??
            (($this->isUnique ? 'uniq_' : 'index_') . strtolower(implode('_', $this->columnNames)));
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

    #[Pure] public function getSql(string $tableName): string
    {
        $unq = $this->isUnique() ? 'UNIQUE' : '';
        $cols = implode(', ', $this->getColumnNames());

        return "CREATE $unq INDEX {$this->getName()} ON $tableName ($cols)";
    }


}
