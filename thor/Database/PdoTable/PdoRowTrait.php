<?php

/**
 * @package          Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Database\PdoTable;

use JetBrains\PhpStorm\Pure;
use JetBrains\PhpStorm\ArrayShape;
use Thor\Database\PdoTable\Attributes\{PdoRow, PdoColumn, PdoAttributesReader};

/**
 * Trait PdoRowTrait: implements PdoRowInterface with Pdo Attributes.
 * @package   Thor\Database\PdoExtension
 *
 * @since     2020-10
 * @version   1.0
 * @author    Trehinos
 * @copyright Author
 * @license   MIT
 */
trait PdoRowTrait
{

    private static array $tableDefinition = [];

    protected array $formerPrimaries = [];

    public function __construct(
        protected array $primaries = []
    ) {
    }

    final public static function getIndexes(): array
    {
        return static::getTD()['indexes'];
    }

    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    protected static function getTD(): array
    {
        return static::$tableDefinition[static::class] ??= PdoAttributesReader::pdoRowInfo(static::class);
    }

    /**
     * @template T
     *
     * @param class-string<T> $className
     * @param array           $row
     * @param bool            $fromDb
     * @param mixed           ...$constructorArguments
     *
     * @return T
     */
    public static function instantiateFromRow(
        string $className,
        array $row,
        bool $fromDb = false,
        mixed ...$constructorArguments
    ): object {
        $rowObj = new $className(...$constructorArguments);
        $rowObj->fromPdoArray($row, $fromDb);
        return $rowObj;
    }

    public function toPdoArray(): array
    {
        $pdoArray = [];
        foreach (static::getPdoColumnsDefinitions() as $columnName => $pdoColumn) {
            if (in_array($columnName, static::getPrimaryKeys())) {
                $pdoArray[$columnName] = $pdoColumn->toSql($this->primaries[$columnName] ?? null);
                continue;
            }
            $propertyName = str_replace(' ', '_', $columnName);
            $pdoArray[$columnName] = $pdoColumn->toSql($this->$propertyName ?? null);
        }
        return $pdoArray;
    }

    /**
     * @return PdoColumn[]
     */
    final public static function getPdoColumnsDefinitions(): array
    {
        return array_combine(
            array_map(fn(PdoColumn $column) => $column->getName(), static::getTD()['columns']),
            array_values(static::getTD()['columns'])
        );
    }

    /**
     * @return string[] an array of field name(s).
     */
    final public static function getPrimaryKeys(): array
    {
        return static::getTableDefinition()->getPrimaryKeys();
    }

    final public static function getTableDefinition(): PdoRow
    {
        return static::getTD()['row'];
    }

    public function fromPdoArray(array $pdoArray, bool $fromDb = false): void
    {
        $this->primaries = [];
        $this->formerPrimaries = [];
        foreach ($pdoArray as $columnName => $columnSqlValue) {
            $phpValue = static::getPdoColumnsDefinitions()[$columnName]->toPhp($columnSqlValue);
            if (in_array($columnName, static::getPrimaryKeys())) {
                $this->primaries[$columnName] = $phpValue;
                if ($fromDb) {
                    $this->formerPrimaries[$columnName] = $phpValue;
                }
                continue;
            }
            $propertyName = str_replace(' ', '_', $columnName);
            $this->$propertyName = $phpValue;
        }
    }

    /**
     * @return array get primary values.
     */
    final public function getPrimary(): array
    {
        return $this->primaries;
    }

    final public function getFormerPrimary(): array
    {
        return $this->formerPrimaries;
    }

    final public function setPrimary(array $primary): void
    {
        $this->primaries = $primary;
    }

    final public function reset(): void
    {
        $this->primaries = $this->formerPrimaries;
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
