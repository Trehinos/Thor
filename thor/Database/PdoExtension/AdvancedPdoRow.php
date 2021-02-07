<?php

namespace Thor\Database\PdoExtension;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use ReflectionAttribute;
use ReflectionClass;
use Thor\Database\PdoExtension\Attributes\PdoColumn;
use Thor\Database\PdoExtension\Attributes\PdoIndex;
use Thor\Database\PdoExtension\Attributes\PdoRow;

/**
 * Trait AdvancedPdoRow: implements PdoRowInterface with Pdo Attributes.
 * @package Thor\Database\PdoExtension
 *
 * @since 2020-10
 * @version 1.0
 * @author Sébastien Geldreich
 * @copyright Author
 * @license MIT
 */
#[PdoRow(primary: ['id'], auto: 'id')]
#[PdoIndex(['public_id'], null, true)]
#[PdoColumn('id', 'INTEGER', 'integer')]
#[PdoColumn('public_id', 'VARCHAR(255)', 'string')]
trait AdvancedPdoRow
{

    private static ?array $tableDefinition = null;

    public function __construct(
        private ?string $public_id = null,
        private array $primaries = [null]
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

    /**
     * @throws Exception
     */
    final public function getPublicId(): ?string
    {
        return $this->public_id;
    }

    /**
     * @throws Exception
     */
    public function generatePublicId(): void
    {
        $this->public_id = bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(4)) .
            '-' . bin2hex(random_bytes(4));
    }

    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array'])]
    private static function getRowsAndColumnsFromClass(
        ReflectionClass $rc
    ): array {
        $row = ($rc->getAttributes(PdoRow::class)[0] ?? null)?->newInstance();
        $columns = array_map(
            fn(ReflectionAttribute $ra) => $ra->newInstance(),
            $rc->getAttributes(PdoColumn::class)
        );
        $indexes = array_map(
            fn(ReflectionAttribute $ra) => $ra->newInstance(),
            $rc->getAttributes(PdoIndex::class)
        );

        foreach ($rc->getTraits() as $t) {
            ['row' => $pRow, 'columns' => $pColumns, 'indexes' => $pIndexes] =
                self::getRowsAndColumnsFromClass($t);
            ['row' => $row, 'columns' => $columns, 'indexes' => $indexes] =
                self::_merge($pRow, $row, $pColumns, $columns, $pIndexes, $indexes);
        }

        if ($p = $rc->getParentClass()) {
            ['row' => $pRow, 'columns' => $pColumns, 'indexes' => $pIndexes] =
                self::getRowsAndColumnsFromClass($p);
            ['row' => $row, 'columns' => $columns, 'indexes' => $indexes] =
                self::_merge($pRow, $row, $pColumns, $columns, $pIndexes, $indexes);
        }

        return ['row' => $row, 'columns' => $columns, 'indexes' => $indexes];
    }

    private static function _merge(
        ?PdoRow $rowA,
        ?PdoRow $rowB,
        array $columnsA,
        array $columnsB,
        array $indexA,
        array $indexB
    ): array {
        return [
            'row' => ($rowA === null) ? $rowB :
                new PdoRow(
                    $rowB?->getTableName() ?? $rowA->getTableName(),
                    array_merge($rowA->getPrimaryKeys(), $rowB?->getPrimaryKeys() ?? []),
                    $rowB?->getAutoColumnName() ?? $rowA->getAutoColumnName(),
                )
            ,
            'columns' => array_merge($columnsA, $columnsB),
            'indexes' => array_merge($indexA, $indexB)
        ];
    }

    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array'])]
    private static function getTD(): array
    {
        return static::$tableDefinition ??=
            self::getRowsAndColumnsFromClass(new ReflectionClass(static::class));
    }

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
        /**
         * @var PdoColumn $pdoColumn
         */
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
                $this->primaries[$columnName] = self::getPdoColumnsDefinitions()[$columnName]->toPhp($columnSqlValue);
                continue;
            }
            $this->$columnName = self::getPdoColumnsDefinitions()[$columnName]->toPhp($columnSqlValue);
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
