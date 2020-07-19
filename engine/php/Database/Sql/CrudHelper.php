<?php

namespace Thor\Database\Sql;

use Thor\Database\PdoRequester;
use Thor\Database\PdoRowInterface;

/**
 * Class CrudHelper : SQL generator facade
 *
 *
 * @package Thor\Database\Sql
 */
final class CrudHelper
{

    private PdoRequester $requester;
    private string $className;

    public function __construct(string $className, PdoRequester $requester)
    {
        $this->requester = $requester;
        $this->className = $className;
    }

    public function table(): string
    {
        return strtolower(substr($this->className, strrpos($this->className, '\\') + 1));
    }

    public function listAll(): array
    {
        $rows = $this->requester->request("SELECT * FROM {$this->table()}", [])->fetchAll();
        if (empty($rows)) {
            return [];
        }

        $rowsObjs = [];
        foreach ($rows as $row) {
            $className = $this->className;
            $rowObj = new $className();
            $rowObj->fromPdoArray($row);
            $rowsObjs[] = $rowObj;
        }

        return $rowsObjs;
    }

    /**
     * @param PdoRowInterface $row
     *
     * @return string the public_id
     */
    public function createOne(PdoRowInterface $row): string
    {
        [$columns, $marks, $values] = self::compileRowValues($row);
        $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $values);

        return $row->getPublicId();
    }

    /**
     * @param PdoRowInterface[] $rows
     *
     * @return int number of rows written
     */
    public function createMultiple(array $rows): int
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
        $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $allValues);

        return count($rows);
    }

    private static function compileRowValues(PdoRowInterface $row): array
    {
        $row->generatePublicId();
        $pdoArray = $row->toPdoArray();
        unset($pdoArray['id']);

        $columns = implode(', ', array_keys($pdoArray));
        $values = implode(', ', array_fill(0, count($pdoArray), '?'));

        return [$columns, $values, array_values($pdoArray)];
    }

    private function instantiateFromRow(array $row)
    {
        $className = $this->className;
        $rowObj = new $className();
        $rowObj->fromPdoArray($row);

        return $rowObj;

    }

    public function readOne(string $id): ?PdoRowInterface
    {
        $row = $this->requester->request("SELECT * FROM {$this->table()} WHERE id = ?", [$id])->fetchAll()[0] ?? [];
        if (empty($row)) {
            return null;
        }

        return $this->instantiateFromRow($row);
    }

    public function readOneBy(Criteria $criteria): ?PdoRowInterface
    {
        $sql = (($t_sql = $criteria->getSql()) === '') ? '' : "WHERE $t_sql";
        $row = $this->requester->request("SELECT * FROM {$this->table()} $sql", $criteria->getParams())->fetchAll()[0] ?? [];
        if (empty($row)) {
            return null;
        }

        return $this->instantiateFromRow($row);
    }

    public function readOneFromPid(string $pid): ?PdoRowInterface
    {
        $row = $this->requester->request(
                "SELECT * FROM {$this->table()} WHERE public_id = ?",
                [$pid]
            )->fetchAll()[0] ?? [];
        if (empty($row)) {
            return null;
        }

        return $this->instantiateFromRow($row);
    }

    /**
     * @param Criteria $criteria
     * @return PdoRowInterface[]
     */
    public function readMultipleBy(Criteria $criteria): array
    {
        $sql = (($t_sql = $criteria->getSql()) === '') ? '' : "WHERE $t_sql";
        $rows = $this->requester->request("SELECT * FROM {$this->table()} $sql", $criteria->getParams())->fetchAll() ?? [];
        if (empty($row)) {
            return [];
        }

        $objs = [];
        foreach ($rows as $row) {
            $objs[] = $this->instantiateFromRow($row);
        }
        return $objs;
    }

    public function updateOne(PdoRowInterface $row)
    {
        $pdoArray = $row->toPdoArray();
        $sets = implode(', ', array_map(fn(string $col) => "$col = ?", array_keys($pdoArray)));

        $this->requester->execute(
            "UPDATE {$this->table()} SET $sets WHERE id=?",
            array_values($pdoArray) + [$row->getId()]
        );
    }

    public function deleteOne(PdoRowInterface $row)
    {
        $this->requester->execute("DELETE FROM {$this->table()} WHERE id=?", [$row->getId()]);
    }

}
