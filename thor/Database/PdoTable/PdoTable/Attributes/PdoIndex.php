<?php

namespace Thor\Database\PdoTable\PdoTable\Attributes;

use Attribute;
use JetBrains\PhpStorm\Pure;

/**
 * Represents an table index.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
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

    /**
     * Gets the index's name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the column names of this index.
     */
    public function getColumnNames(): array
    {
        return $this->columnNames;
    }

    /**
     * Returns true if the index is an unique index.
     */
    public function isUnique(): bool
    {
        return $this->isUnique;
    }

    /**
     * Gets the constraint's SQL.
     */
    #[Pure]
    public function getSql(): string
    {
        $unq = $this->isUnique() ? ' UNIQUE' : '';
        $cols = implode(', ', $this->getColumnNames());

        return "CONSTRAINT$unq INDEX {$this->getName()} ($cols)";
    }


}
