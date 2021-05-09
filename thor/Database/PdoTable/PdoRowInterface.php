<?php

/**
 * @package Trehinos/Thor/Database/PdoTable
 * @copyright (2021) Sébastien GELDREICH
 * @license MIT
 */

namespace Thor\Database\PdoTable;

use Thor\Database\PdoTable\Attributes\PdoRow;

interface PdoRowInterface
{

    // <-> SQL Methods

    public static function getTableDefinition(): PdoRow;

    /**
     * @return array an array of 'column_name' => 'SQL_COLUMN_TYPE(SIZE)'.
     */
    public static function getPdoColumnsDefinitions(): array;

    /**
     * @return string[] an array of field name(s).
     */
    public static function getPrimaryKeys(): array;

    public static function getIndexes(): array;

    public function toPdoArray(): array;

    public function fromPdoArray(array $pdoArray): void;


    // DEFAULT ACCESSORS & METHODS

    /**
     * @return array get primary keys in an array of 'column_name' => PHP_value.
     */
    public function getPrimary(): array;

    /**
     * @return string get primary keys in a concatenated string.
     */
    public function getPrimaryString(): string;

}
