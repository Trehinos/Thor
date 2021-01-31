<?php

namespace Thor\Database\PdoExtension;

use Exception;
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
trait AdvancedPdoRow
{

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
    protected array $attributes = ['id' => null, 'public_id' => null];

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
        return static::getDefinitionHelper()->getTableDefinition(static::getTableName())['columns'] ?? [];
    }

    abstract public static function getTableName(): string;

    final public function toPdoArray(): array
    {
        $pdoArray = [];
        foreach (static::getPdoColumnsDefinitions() as $columnName => $columnDef_unused) {
            $pdoArray[$columnName] = $this->attributes[$columnName] ?? null;
        }
        return $pdoArray;
    }

    final public function fromPdoArray(array $pdoArray): void
    {
        $this->attributes = [];
        foreach (static::getPdoColumnsDefinitions() as $columnName => $columnDef_unused) {
            $this->attributes[$columnName] = $pdoArray[$columnName] ?? null;
        }
    }

}
