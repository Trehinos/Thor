<?php

namespace Thor\Database\PdoTable\PdoRecord;

use Thor\Database\PdoTable\Criteria;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoTable\PdoRow\RowInterface;
use Thor\Database\PdoTable\SchemaHelper;

interface RecordInterface extends RowInterface
{

    public function getCrudHelper(): CrudHelper;
    public function getSchemaHelper(): SchemaHelper;

    public function createTable(): bool;

    /**
     * - Returns `true` if the record exists in the database and is synchronized with this object.
     * - Returns `false` if the record exists in the database but is not synchronized with this object.
     * - Returns `null` if the record does not exist in the database or if this object is empty.
     *
     * @return ?bool
     */
    public function synced(): ?bool;

    /**
     * Inserts this PdoRowInterface in the corresponding SQL Table.
     *
     * @return bool
     */
    public function insert(): bool;

    /**
     * Updates this PdoRowInterface in the corresponding SQL Table.
     *
     * @return bool
     */
    public function update(): bool;

    /**
     * Inserts or updates this PdoRowInterface in the corresponding SQL Table.
     *
     * @return bool
     */
    public function upsert(): bool;

    /**
     * Deletes this PdoRowInterface in the corresponding SQL Table.
     *
     * @return bool
     */
    public function delete(): bool;

    /**
     * Change all the data of this object with new data from the database.
     *
     * @param Criteria|null $criteria
     *
     * @return bool
     */
    public function reload(?Criteria $criteria = null): bool;

    public function dropTable(): bool;

}
