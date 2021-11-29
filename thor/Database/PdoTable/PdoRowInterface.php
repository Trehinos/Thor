<?php

namespace Thor\Database\PdoTable;

use Thor\Database\PdoTable\Attributes\PdoTable;
use Thor\Database\PdoTable\Attributes\PdoIndex;

/**
 * Represents a class describing an SQL table.
 *
 * The class implementing this interface can then be used with CrudHelper or SchemaHelper.
 *
 * Use PdoRowTrait, AbstractPdoRow or BasePdoRow to implement easily all methods and use with PdoTable\Attributes.
 *
 * @see              PdoRowTrait
 * @see              AbstractPdoRow
 * @see              BasePdoRow
 * @see              CrudHelper
 * @see              SchemaHelper
 *
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
interface PdoRowInterface
{

    /**
     * Gets the PdoTable representing the table information of this PdoRowInterface.
     */
    public static function getPdoTable(): PdoTable;

    /**
     * @return array an array of 'column_name' => 'SQL_COLUMN_TYPE(SIZE)'.
     */
    public static function getPdoColumnsDefinitions(): array;

    /**
     * @return string[] an array of field name(s).
     */
    public static function getPrimaryKeys(): array;

    /**
     * @return PdoIndex[] an array of PdoIndex containing indexes information.
     */
    public static function getIndexes(): array;

    /**
     * @return array an array representation of this object which is the same as it would be returned by
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
     * @return array get primary keys in an array of 'column_name' => PHP_value.
     */
    public function getPrimary(): array;

    /**
     * @return array get primary keys as loaded from DB. Empty if not loaded from DB.
     */
    public function getFormerPrimary(): array;

    /**
     * @return string get primary keys in a concatenated string.
     */
    public function getPrimaryString(): string;

}
