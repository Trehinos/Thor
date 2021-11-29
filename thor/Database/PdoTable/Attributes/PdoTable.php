<?php

namespace Thor\Database\PdoTable\Attributes;

use Attribute;

/**
 * Describe a PdoTable attribute. Use this attribute on a PdoRowInterface implementor
 * to specify the corresponding table name, primary keys and auto-increment.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
#[Attribute(Attribute::TARGET_CLASS)]
class PdoTable
{

    public function __construct(
        private ?string $tableName = null,
        private array $primary = [],
        private ?string $auto = null
    ) {
    }

    /**
     * Returns the SQL table name.
     */
    public function getTableName(): ?string
    {
        return $this->tableName;
    }

    /**
     * Returns the primary keys of the class.
     */
    public function getPrimaryKeys(): array
    {
        return $this->primary;
    }

    /**
     * Returns the auto-increment column name. Can be null if none is defined.
     */
    public function getAutoColumnName(): ?string
    {
        return $this->auto;
    }

}
