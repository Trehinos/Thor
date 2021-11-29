<?php

namespace Thor\Database\PdoTable;

/**
 * Class CrudHelper : SQL CRUD operation requester for PdoRows.
 *
 * @template         T
 * @package          Thor\Database\PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
interface CrudInterface
{

    /**
     * List all rows of the entity managed by this Crud.
     *
     * @return T[]
     */
    public function listAll(): array;

    /**
     * Create one row in DB.
     *
     * @param T $row
     *
     * @return string primary string/key
     */
    public function createOne(PdoRowInterface $row): string;

    /**
     * Creates multiple rows in DB.
     *
     * @param T[] $rows
     *
     * @return bool
     */
    public function createMultiple(array $rows): bool;

    /**
     * Gets the table name.
     *
     * @return string
     */
    public function table(): string;

    /**
     * Gets one row from its primary keys.
     *
     * Returns `null` if the row has not been found.
     *
     * @param array $primaries
     *
     * @return T|null
     */
    public function readOne(array $primaries): ?PdoRowInterface;

    /**
     * Gets one row from the specified criteria.
     *
     * Returns `null` if the row has not been found.
     *
     * @param Criteria $criteria
     *
     * @return T|null
     */
    public function readOneBy(Criteria $criteria): ?PdoRowInterface;

    /**
     * Gets multiple rows from the specified criteria.
     *
     * @param Criteria $criteria
     *
     * @return T[]
     */
    public function readMultipleBy(Criteria $criteria): array;

    /**
     * Updates all column of the corresponding row in DB.
     *
     * Returns true if the statement has correctly been executed.
     *
     * @param T $row
     *
     * @return bool
     */
    public function updateOne(PdoRowInterface $row): bool;

    /**
     * Delete the corresponding row in DB.
     *
     * Returns true if the statement has correctly been executed.
     *
     * @param T $row
     *
     * @return bool
     */
    public function deleteOne(PdoRowInterface $row): bool;

}
