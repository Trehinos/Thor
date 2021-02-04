<?php

namespace Thor\Database\PdoExtension;

use Exception;
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
    private static ?string $tableName = null;

    /**
     * All attributes of the PdoRow.
     */
    protected array $attributes;

    public function __construct()
    {
        $this->attributes['id'] = null;
        $this->attributes['public_id'] = null;
    }

    public function getId(): ?int
    {
        return $this->attributes['id'] ?? null;
    }

    public function setId(int $id): void
    {
        $this->attributes['id'] = $id;
    }

    /**
     * @throws Exception
     */
    public function getPublicId(): ?string
    {
        if (null === $this->attributes['public_id']) {
            $this->generatePublicId();
        }
        return $this->attributes['public_id'] ?? null;
    }

    public function setPublicId(string $public_id): void
    {
        $this->attributes['public_id'] = $public_id;
    }

    /**
     * @throws Exception
     */
    public function generatePublicId(): void
    {
        $this->attributes['public_id'] = bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(2)) .
            '-' . bin2hex(random_bytes(4)) .
            '-' . bin2hex(random_bytes(4));
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
            /** @var PdoColumn */
            $pdoColumn = $column->newInstance();
            $pdoDefs[$pdoColumn->getName()] = $pdoColumn;
        }

        return static::$tableDefinition = $pdoDefs;
    }

    final public static function getTableName(): string
    {
        if (null !== static::$tableName) {
            return static::$tableName;
        }
        $rc = new ReflectionClass(static::class);
        return static::$tableName = $rc->getAttributes(PdoRow::class)[0]->getName();
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
        foreach ($this->getPdoColumnsDefinitions() as $columnName => $pdoColumn) {
            $pdoArray[$columnName] = $pdoColumn->toSql($this->attributes[$columnName] ?? null);
        }
        return $pdoArray;
    }

    final public function fromPdoArray(array $pdoArray): void
    {
        $this->attributes = [];
        /**
         * @var PdoColumn $pdoColumn
         */
        foreach ($this->getPdoColumnsDefinitions() as $columnName => $pdoColumn) {
            $this->attributes[$columnName] = $pdoColumn->toPhp($pdoArray[$columnName] ?? null);
        }
    }

}
