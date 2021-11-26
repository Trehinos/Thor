<?php

/**
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoTable\Attributes;

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

    #[Pure] public function getSql(): string
    {
        $unq = $this->isUnique() ? ' UNIQUE' : '';
        $cols = implode(', ', $this->getColumnNames());

        return "CONSTRAINT$unq INDEX {$this->getName()} ($cols)";
    }


}
