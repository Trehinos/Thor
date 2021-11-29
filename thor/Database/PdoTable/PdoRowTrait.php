<?php

namespace Thor\Database\PdoTable;

use ReflectionException;
use JetBrains\PhpStorm\Pure;
use JetBrains\PhpStorm\ArrayShape;
use Thor\Database\PdoTable\Attributes\{PdoIndex, PdoTable, PdoColumn, PdoAttributesReader};

/**
 * Trait PdoRowTrait: implements PdoRowInterface with Pdo Attributes.
 *
 * @package   Thor\Database\PdoTable
 *
 * @since     2020-10
 * @version   1.0
 * @author    Trehinos
 * @copyright Author
 * @license   MIT
 */
trait PdoRowTrait
{

    private static array $tablesAttributes = [];

    protected array $formerPrimaries = [];

    public function __construct(
        protected array $primaries = []
    ) {
    }

    /**
     * @return PdoIndex[] an array of PdoIndex containing indexes information.
     *
     * @throws ReflectionException
     */
    final public static function getIndexes(): array
    {
        return static::getTableAttributes()['indexes'];
    }

    /**
     * Gets all Thor\Database\PdoTable\Attributes\Pdo* attributes.
     *
     * @throws ReflectionException
     */
    #[ArrayShape(['row' => PdoTable::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    public static function getTableAttributes(): array
    {
        return static::$tablesAttributes[static::class] ??= PdoAttributesReader::pdoTableInformation(static::class);
    }

    /**
     * @return array an array representation of this object which is the same as it would be returned by
     *               PDOStatement::fetch().
     *
     * @throws ReflectionException
     */
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
     * @return array an array of 'column_name' => 'SQL_COLUMN_TYPE(SIZE)'.
     *
     * @throws ReflectionException
     */
    final public static function getPdoColumnsDefinitions(): array
    {
        return array_combine(
            array_map(fn(PdoColumn $column) => $column->getName(), static::getTableAttributes()['columns']),
            array_values(static::getTableAttributes()['columns'])
        );
    }

    /**
     * @return string[] an array of field name(s).
     *
     * @throws ReflectionException
     */
    final public static function getPrimaryKeys(): array
    {
        return static::getPdoTable()->getPrimaryKeys();
    }

    /**
     * Gets the PdoTable representing the table information of this PdoRowInterface.
     *
     * @throws ReflectionException
     */
    final public static function getPdoTable(): PdoTable
    {
        return static::getTableAttributes()['row'];
    }

    /**
     * This method hydrates the object from the $pdoArray array.
     *
     * If $fromDb is true, this equality MUST be true :  getFormerPrimary() === getPrimary(), after this method.
     *
     * @throws ReflectionException
     */
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
     * @return array get primary keys in an array of 'column_name' => PHP_value.
     */
    final public function getPrimary(): array
    {
        return $this->primaries;
    }

    /**
     * @return array get primary keys as loaded from DB. Empty if not loaded from DB.
     */
    final public function getFormerPrimary(): array
    {
        return $this->formerPrimaries;
    }

    /**
     * Copy formerPrimary on primary array
     */
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

    /**
     * Sets primary key(s).
     */
    final public function setPrimary(array $primary): void
    {
        $this->primaries = $primary;
    }

}
