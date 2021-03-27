<?php

namespace Thor\Database\PdoTable;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Thor\Database\PdoTable\Attributes\PdoAttributesReader;
use Thor\Database\PdoTable\Attributes\PdoColumn;
use Thor\Database\PdoTable\Attributes\PdoRow;

/**
 * Trait PdoRowTrait: implements PdoRowInterface with Pdo Attributes.
 * @package Thor\Database\PdoExtension
 *
 * @since 2020-10
 * @version 1.0
 * @author Trehinos
 * @copyright Author
 * @license MIT
 */
trait PdoRowTrait
{

    private static ?array $tableDefinition = null;

    public function __construct(
        protected array $primaries = []
    ) {
    }

    /**
     * @return string[] an array of field name(s).
     */
    final public static function getPrimaryKeys(): array
    {
        return self::getTableDefinition()->getPrimaryKeys();
    }

    final public static function getIndexes(): array
    {
        return self::getTD()['indexes'];
    }

    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    private static function getTD(): array
    {
        return static::$tableDefinition ??= PdoAttributesReader::pdoRowInfo(static::class);
    }

    /**
     * @return PdoColumn[]
     */
    final public static function getPdoColumnsDefinitions(): array
    {
        return array_combine(
            array_map(fn(PdoColumn $column) => $column->getName(), self::getTD()['columns']),
            array_values(self::getTD()['columns'])
        );
    }

    final public static function getTableDefinition(): PdoRow
    {
        return self::getTD()['row'];
    }

    final public function toPdoArray(): array
    {
        $pdoArray = [];
        foreach (self::getPdoColumnsDefinitions() as $columnName => $pdoColumn) {
            if (in_array($columnName, static::getPrimaryKeys())) {
                $pdoArray[$columnName] = $pdoColumn->toSql($this->primaries[$columnName] ?? null);
                continue;
            }
            $pdoArray[$columnName] = $pdoColumn->toSql($this->$columnName ?? null);
        }
        return $pdoArray;
    }

    final public function fromPdoArray(array $pdoArray): void
    {
        $this->primaries = [];
        foreach ($pdoArray as $columnName => $columnSqlValue) {
            if (in_array($columnName, static::getPrimaryKeys())) {
                $this->primaries[$columnName] = static::getPdoColumnsDefinitions()[$columnName]->toPhp($columnSqlValue);
                continue;
            }
            $this->$columnName = static::getPdoColumnsDefinitions()[$columnName]->toPhp($columnSqlValue);
        }
    }

    /**
     * @return array get primary values.
     */
    final public function getPrimary(): array
    {
        return $this->primaries;
    }

    final public function setPrimary(array $primary): void
    {
        $this->primaries = $primary;
    }

    /**
     * @return string get primary keys in a concatenated string.
     */
    #[Pure]
    final public function getPrimaryString(): string
    {
        return implode('-', $this->primaries);
    }

}
