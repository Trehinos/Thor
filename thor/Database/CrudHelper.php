<?php

namespace Thor\Database;

use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoExtension\PdoRow;
use Thor\Database\PdoExtension\PdoRowInterface;
use Thor\Database\Sql\Criteria;

/**
 * Class CrudHelper : SQL CRUD operation requester for PdoRows.
 *
 * @package Thor\Database\Sql
 */
final class CrudHelper
{

    private ?string $tableName = null;

    /**
     * CrudHelper constructor.
     * Creates a new CRUD requester to manage PdoRows
     *
     * @param string $className
     * @param PdoRequester $requester
     */
    public function __construct(
        private string $className,
        private PdoRequester $requester
    ) {
    }

    #[Pure]
    public function table(): string
    {
        if (null !== $this->tableName) {
            return $this->tableName;
        }
        $rc = new ReflectionClass($this->className);
        $pdoRowAttrs = $rc->getAttributes(PdoRow::class);

        if (empty($pdoRowAttrs)) {
            return strtolower(substr($this->className, strrpos($this->className, '\\') + 1));
        }

        return $this->tableName = $pdoRowAttrs[0]->newInstance()->getTableName();
    }

    public function listAll(): array
    {
        $rows = $this->requester->request("SELECT * FROM {$this->table()}", [])->fetchAll();
        if (empty($rows)) {
            return [];
        }

        $rowsObjs = [];
        foreach ($rows as $row) {
            $rowObj = self::instantiateFromRow($this->className, $row);
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

    private static function compileRowValues(PdoRowInterface $row): array
    {
        $row->generatePublicId();
        $pdoArray = $row->toPdoArray();
        unset($pdoArray['id']);

        $columns = implode(', ', array_keys($pdoArray));
        $values = implode(', ', array_fill(0, count($pdoArray), '?'));

        return [$columns, $values, array_values($pdoArray)];
    }

    public function readOne(string $id): mixed
    {
        return $this->readOneBy(new Criteria(['id' => $id]));
    }

    public function readOneFromPid(string $pid): mixed
    {
        return $this->readOneBy(new Criteria(['public_id' => $pid]));
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

        return self::instantiateFromRow($this->className, $row);
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
            $objs[] = self::instantiateFromRow($this->className, $row);
        }

        return $objs;
    }

    public function updateOne(PdoRowInterface $row): bool
    {
        $pdoArray = $row->toPdoArray();
        $sets = implode(', ', array_map(fn(string $col) => "$col = ?", array_keys($pdoArray)));

        return $this->requester->execute(
            "UPDATE {$this->table()} SET $sets WHERE id = ?",
            array_merge(array_values($pdoArray), [$row->getId()])
        );
    }

    public function deleteOne(PdoRowInterface $row): bool
    {
        return $this->requester->execute("DELETE FROM {$this->table()} WHERE id=?", [$row->getId()]);
    }

    public static function instantiateFromRow(string $className, array $row): mixed
    {
        $rowObj = new $className();
        $rowObj->fromPdoArray($row);
        return $rowObj;
    }

}
