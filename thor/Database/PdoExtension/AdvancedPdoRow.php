<?php

namespace Thor\Database\PdoExtension;

use Exception;
use ReflectionClass;
use Thor\Database\DefinitionHelper;

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
     * Get the DefinitionHelper which is used to resolve columns.
     * To be overwritten by child class.
     */
    public static function getDefinitionHelper(): ?DefinitionHelper
    {
        return null;
    }

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

    final public function getPdoColumnsDefinitions(): array
    {
        if (null !== self::$tableDefinition) {
            return self::$tableDefinition;
        }
        $rc = new ReflectionClass($this::class);
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
            /** @var PdoColumn */ $pdoColumn = $column->newInstance();
            $pdoDefs[$pdoColumn->getName()] = $pdoColumn->getSqlType();
        }

        return self::$tableDefinition = $pdoDefs;
    }

    final public function getTableName(): string
    {
        if (null !== self::$tableName) {
            return self::$tableName;
        }
        $rc = new ReflectionClass($this);
        return self::$tableName = $rc->getAttributes(PdoRow::class)[0]->getName();
    }

    final public function toPdoArray(): array
    {
        $pdoArray = [];
        foreach ($this->getPdoColumnsDefinitions() as $columnName => $columnDef_unused) {
            $pdoArray[$columnName] = $this->attributes[$columnName] ?? null;
        }
        return $pdoArray;
    }

    final public function fromPdoArray(array $pdoArray): void
    {
        $this->attributes = [];
        foreach ($this->getPdoColumnsDefinitions() as $columnName => $columnDef_unused) {
            $this->attributes[$columnName] = $pdoArray[$columnName] ?? null;
        }
    }

}
