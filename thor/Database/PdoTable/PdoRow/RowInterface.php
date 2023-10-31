<?php

namespace Thor\Database\PdoTable\PdoRow;

use Thor\Database\PdoTable\PdoRow\Attributes\Table;

/**
 * Represents a class describing an SQL table.
 *
 * The class implementing this interface can then be used with CrudHelper or SchemaHelper.
 *
 * Use PdoRowTrait, AbstractPdoRow or BasePdoRow to implement easily all methods and use with PdoTable\Attributes.
 *
 * @see              PdoRowTrait
 * @see              AbstractRow
 * @see              Row
 * @see              CrudHelper
 * @see              SchemaHelper
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
interface RowInterface
{

    /**
     * Gets the PdoTable representing the table information of this PdoRowInterface.
     */
    public static function getPdoTable(): Table;

    /**
     * Returns an array of 'column_name' => 'SQL_COLUMN_TYPE(SIZE)'.
     */
    public static function getPdoColumnsDefinitions(): array;

    /**
     * Returns an array of field name(s).
     */
    public static function getPrimaryKeys(): array;

    /**
     * Returns an array of PdoIndex containing indexes information.
     */
    public static function getIndexes(): array;

    /**
     * Returns an array representation of this object which is the same as it would be returned by
     *               PDOStatement::fetch().
     */
    public function toPdoArray(): array;

    /**
     * This method hydrates the object from the $pdoArray array.
     *
     * If $fromDb is true, this equality MUST be true :  getFormerPrimary() === getPrimary(), after this method.
     */
    public function fromPdoArray(array $pdoArray, bool $fromDb = false): void;

    /**
     * Copy formerPrimary on primary array
     */
    public function reset(): void;

    /**
     * Get primary keys in an array of 'column_name' => PHP_value.
     */
    public function getPrimary(): array;

    /**
     * Get primary keys as loaded from DB. Empty if not loaded from DB.
     */
    public function getFormerPrimary(): array;

    /**
     * Get primary keys in a concatenated string.
     */
    public function getPrimaryString(): string;

}
