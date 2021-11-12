<?php

/**
 * @package Trehinos/Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoTable;

use Exception;
use JetBrains\PhpStorm\Pure;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoTable\Attributes\PdoIndex;

/**
 * Class CrudHelper : SQL CRUD operation requester for PdoRows.
 *
 * @package Thor\Database\Sql
 */
final class PdoHelper
{


    /**
     * CrudHelper constructor.
     * Creates a new CRUD requester to manage PdoRows
     *
     * @param string       $tableName
     * @param PdoRequester $requester
     * @param string[]     $primary
     * @param PdoIndex[]   $indexes
     */
    public function __construct(
        private string $tableName,
        private PdoRequester $requester,
        private array $primary = [],
        private array $indexes = []
    ) {
    }

    public function listAll(): array
    {
        return $this->requester->request("SELECT * FROM {$this->table()}", [])->fetchAll();
    }

    #[Pure]
    public function table(): string
    {
        return $this->tableName;
    }

    /**
     * @param array $row
     *
     * @return string the public_id
     *
     * @throws Exception
     */
    public function createOne(array $row): string
    {
        [$columns, $marks, $values] = self::compileRowValues($row);
        $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $values);
        return $row[$this->primary[0]];
    }

    /**
     * @param array $row
     *
     * @return array
     *
     * @throws Exception
     */
    private static function compileRowValues(array $row): array
    {
        $columns = implode(', ', array_keys($row));
        $values = implode(', ', array_fill(0, count($row), '?'));

        return [$columns, $values, array_values($row)];
    }

    /**
     * @param array[] $rows
     *
     * @return bool
     *
     * @throws Exception
     */
    public function createMultiple(array $rows): bool
    {
        $allValues = [];
        $sqlArray = [];
        $columns = [];

        foreach ($rows as $row) {
            [$columns, $marks, $values] = self::compileRowValues($row);

            $allValues = array_merge($allValues, $values);
            $sqlArray [] = "($marks)";
        }

        $marks = implode(', ', $sqlArray);
        return $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $allValues);
    }

    public function readOne(array $primaries): ?array
    {
        return $this->readOneBy($this->primaryArrayToCriteria($primaries));
    }

    public function readOneBy(Criteria $criteria): ?array
    {
        $sql = Criteria::getWhere($criteria);
        return $this->requester->request(
                $sql = "SELECT * FROM {$this->table()} $sql",
                $criteria->getParams()
            )->fetchAll()[0] ?? null;
    }

    #[Pure]
    private function primaryArrayToCriteria(
        array $primaries
    ): Criteria {
        $criteria = [];
        foreach ($primaries as $pKey => $pValue) {
            if (in_array($pKey, $this->primary)) {
                $criteria[$pKey] = $pValue;
            }
        }

        return new Criteria($criteria);
    }

    /**
     * @param Criteria $criteria
     *
     * @return array
     */
    public function readMultipleBy(Criteria $criteria): array
    {
        $sql = Criteria::getWhere($criteria);
        $sql = "SELECT * FROM {$this->table()} $sql";
        return $this->requester->request(
                $sql,
                $criteria->getParams()
            )->fetchAll() ?? [];
    }

    public function updateOne(array $row, array $excludeColumns = []): bool
    {
        $pdoArray = $row;
        if (!empty($excludeColumns)) {
            $pdoTmp = [];
            foreach ($row as $key => $item) {
                if (!in_array($key, $excludeColumns)) {
                    $pdoTmp[$key] = $item;
                }
            }
            $pdoArray = $pdoTmp;
        }
        $sets = implode(', ', array_map(fn(string $col) => "$col = ?", array_keys($pdoArray)));

        $primaries = [];
        foreach ($this->primary as $primaryColumnName) {
            $primaries[$primaryColumnName] = $pdoArray[$primaryColumnName] ?? null;
        }
        $criteria = $this->primaryArrayToCriteria($primaries);

        return $this->requester->execute(
            "UPDATE {$this->table()} SET $sets " . Criteria::getWhere($criteria),
            array_merge(array_values($pdoArray), $criteria->getParams())
        );
    }

    public function deleteOne(array $row): bool
    {
        $primaries = [];
        foreach ($this->primary as $primaryColumnName) {
            $primaries[$primaryColumnName] = $row[$primaryColumnName] ?? null;
        }
        $criteria = $this->primaryArrayToCriteria($primaries);
        return $this->requester->execute(
            "DELETE FROM {$this->table()} " . Criteria::getWhere($criteria),
            $criteria->getParams()
        );
    }

}
