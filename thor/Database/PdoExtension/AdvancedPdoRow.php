<?php

namespace Thor\Database\PdoExtension;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;

/**
 * Trait AdvancedPdoRow: implements PdoRowInterface of a table definition extending the default virtual table.
 * @package Thor\Database\PdoExtension
 *
 * @since 2020-10
 * @version 1.0
 * @author Sébastien Geldreich
 * @copyright Author
 * @license MIT
 */
#[PdoColumn('id', 'INTEGER', 'integer')]
#[PdoColumn('public_id', 'VARCHAR(255)', 'string')]
trait AdvancedPdoRow
{

    private static ?array $tableDefinition = null;
    private static ?PdoRow $pdoRowInfo = null;

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
        return self::getPdoRowInfo()->getPrimaryKeys();
    }

    #[Pure]
    #[ArrayShape(['primary' => '?array', 'uniques' => '?array', 'indexes' => '?array', 'auto' => '?string'])]
    final public static function getIndexes(): array
    {
        return self::getPdoRowInfo()->getIndexes();
    }

    /**
     * @throws Exception
     */
    final public function getPublicId(): ?string
    {
        return $this->public_id;
    }

    final public function setPublicId(string $public_id): void
    {
        $this->public_id = $public_id;
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

    private static function getPdoRowInfo(): ?PdoRow
    {
        return static::$pdoRowInfo ??=
            (new ReflectionClass(static::class))->getAttributes(PdoRow::class)[0]->newInstance();
    }

    final public static function getPdoColumnsDefinitions(): array
    {
        if (null !== static::$tableDefinition) {
            return static::$tableDefinition;
        }
        $rc = new ReflectionClass(static::class);
        $columns = $rc->getAttributes(PdoColumn::class);
        foreach ($rc->getTraits() as $trait) {
            $columns = array_merge($columns, $trait->getAttributes(PdoColumn::class));
        }

        $p = $rc;
        while ($p = $p->getParentClass()) {
            $columns = array_merge($columns, $p->getAttributes(PdoColumn::class));
            foreach ($p->getTraits() as $trait) {
                $columns = array_merge($columns, $trait->getAttributes(PdoColumn::class));
            }
        }

        $pdoDefs = [];
        foreach ($columns as $column) {
            $pdoColumn = $column->newInstance();
            $pdoDefs[$pdoColumn->getName()] = $pdoColumn;
        }

        return static::$tableDefinition = $pdoDefs;
    }

    final public static function getTableDefinition(): object
    {
        $rc = new ReflectionClass(static::class);
        return $rc->getAttributes(PdoRow::class)[0]->newInstance();
    }

    final public function toPdoArray(): array
    {
        $pdoArray = [];
        /**
         * @var PdoColumn $pdoColumn
         */
        foreach (self::getPdoColumnsDefinitions() as $columnName => $pdoColumn) {
            if (in_array($columnName,  static::getPrimaryKeys())) {
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
            if (in_array($columnName,  static::getPrimaryKeys())) {
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
    final public function getPrimaryString(): string
    {
        return array_reduce($this->primaries, fn($pString, $pKey) => "$pString$pKey", '');
    }

}
