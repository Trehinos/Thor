<?php

/**
 * @package          Trehinos/Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Database\PdoTable;

use Exception;
use TypeError;
use JetBrains\PhpStorm\Pure;
use Thor\Database\PdoExtension\PdoRequester;

/**
 * Class CrudHelper : SQL CRUD operation requester for PdoRows.
 *
 * @package Thor\Database\Sql
 */
final class CrudHelper
{

    private string $tableName;
    private array $primary;
    private array $indexes;

    /**
     * CrudHelper constructor.
     * Creates a new CRUD requester to manage PdoRows
     *
     * @param string $className which implements PdoRowInterface and use PdoRowTrait trait.
     * @param PdoRequester $requester
     */
    public function __construct(
        private string $className,
        private PdoRequester $requester
    ) {
        if (!class_exists($this->className) || !in_array(PdoRowInterface::class, class_implements($this->className))) {
            throw new TypeError("{$this->className} class not found or not implementing PdoRowInterface...");
        }
        $this->tableName = ($this->className)::getTableDefinition()->getTableName();
        $this->primary = ($this->className)::getPrimaryKeys();
        $this->indexes = ($this->className)::getIndexes();
    }

    public function listAll(): array
    {
        $rows = $this->requester->request("SELECT * FROM {$this->table()}", [])->fetchAll();
        if (empty($rows)) {
            return [];
        }

        $rowsObjs = [];
        foreach ($rows as $row) {
            $rowObj = PdoRowTrait::instantiateFromRow($this->className, $row, true);
            $rowsObjs[] = $rowObj;
        }

        return $rowsObjs;
    }

    #[Pure]
    public function table(): string
    {
        return $this->tableName;
    }

    /**
     * @param PdoRowInterface $row
     *
     * @return string the public_id
     *
     * @throws Exception
     */
    public function createOne(PdoRowInterface $row): string
    {
        if (!empty($row->getFormerPrimary())) {
            throw new PdoRowException(
                "PdoRow with primary string '{$row->getPrimaryString()}' cannot be inserted as it has been loaded from DB."
            );
        }
        [$columns, $marks, $values] = self::compileRowValues($row);
        $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $values);

        if ($row instanceof AbstractPdoRow) {
            return $row->getPublicId();
        }

        return $row->getPrimaryString();
    }

    /**
     * @param PdoRowInterface $row
     *
     * @return array
     *
     * @throws Exception
     */
    private static function compileRowValues(PdoRowInterface $row): array
    {
        if ($row instanceof AbstractPdoRow) {
            $row->generatePublicId();
        }
        $pdoArray = $row->toPdoArray();

        $columns = implode(', ', array_keys($pdoArray));
        $values = implode(', ', array_fill(0, count($pdoArray), '?'));

        return [$columns, $values, array_values($pdoArray)];
    }

    /**
     * @param PdoRowInterface[] $rows
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
            if (!empty($row->getFormerPrimary())) {
                throw new PdoRowException(
                    "PdoRow with primary string '{$row->getPrimaryString()}' cannot be inserted as it has been loaded from DB."
                );
            }
            [$columns, $marks, $values] = self::compileRowValues($row);

            $allValues = array_merge($allValues, $values);
            $sqlArray [] = "($marks)";
        }

        $marks = implode(', ', $sqlArray);
        return $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $allValues);
    }

    public function readOne(array $primaries): mixed
    {
        return $this->readOneBy($this->primaryArrayToCriteria($primaries));
    }

    public function readOneBy(Criteria $criteria): mixed
    {
        $sql = Criteria::getWhere($criteria);
        $row = $this->requester->request(
                $sql = "SELECT * FROM {$this->table()} $sql",
                $criteria->getParams()
            )->fetchAll()[0] ?? [];

        if (empty($row)) {
            return null;
        }

        return PdoRowTrait::instantiateFromRow($this->className, $row, true);
    }

    private function primaryArrayToCriteria(array $primaries): Criteria
    {
        $criteria = [];
        foreach ($this->primary as $primaryKey) {
            $criteria[$primaryKey] = array_shift($primaries);
        }

        return new Criteria($criteria);
    }


    public function readOneFromPid(string $pid): mixed
    {
        return $this->readOneBy(new Criteria(['public_id' => $pid]));
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
        $rows = $this->requester->request(
                $sql,
                $criteria->getParams()
            )->fetchAll() ?? [];


        if (empty($rows)) {
            return [];
        }

        $objs = [];
        foreach ($rows as $row) {
            $objs[] = PdoRowTrait::instantiateFromRow($this->className, $row, true);
        }

        return $objs;
    }

    public function updateOne(PdoRowInterface $row, array $excludeColumns = []): bool
    {
        $pdoArray = $row->toPdoArray();
        if (!empty($excludeColumns)) {
            $pdoTmp = [];
            foreach ($pdoArray as $key => $item) {
                if (!in_array($key, $excludeColumns)) {
                    $pdoTmp[$key] = $item;
                }
            }
            $pdoArray = $pdoTmp;
        }
        $sets = implode(', ', array_map(fn(string $col) => "$col = ?", array_keys($pdoArray)));

        $criteria = $this->primaryArrayToCriteria($row->getFormerPrimary());

        return $this->requester->execute(
            "UPDATE {$this->table()} SET $sets " . Criteria::getWhere($criteria),
            array_merge(array_values($pdoArray), $criteria->getParams())
        );
    }

    public function deleteOne(PdoRowInterface $row): bool
    {
        $criteria = $this->primaryArrayToCriteria($row->getFormerPrimary());
        return $this->requester->execute(
            "DELETE FROM {$this->table()} " . Criteria::getWhere($criteria),
            $criteria->getParams()
        );
    }

}
