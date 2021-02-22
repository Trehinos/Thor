<?php

namespace Thor\Database\PdoTable;

use Exception;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionException;
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
     * @param string $className which implements PdoRowInterface and use AdvancedPdoRow trait.
     * @param PdoRequester $requester
     *
     * @throws ReflectionException
     */
    public function __construct(
        private string $className,
        private PdoRequester $requester
    ) {
        $rc = new ReflectionClass($this->className);
        $this->tableName = $rc->getMethod('getTableDefinition')->invoke(null)->getTableName();
        $this->primary = $rc->getMethod('getPrimaryKeys')->invoke(null);
        $this->indexes = $rc->getMethod('getIndexes')->invoke(null);
    }

    #[Pure]
    public function table(): string
    {
        return $this->tableName;
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
     *
     * @throws Exception
     */
    public function createOne(PdoRowInterface $row): string
    {
        [$columns, $marks, $values] = self::compileRowValues($row);
        $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $values);

        if ($row instanceof AbstractPdoRow) {
            return $row->getPublicId();
        }

        return $row->getPrimaryString();
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
            [$columns, $marks, $values] = self::compileRowValues($row);

            $allValues = array_merge($allValues, $values);
            $sqlArray [] = "($marks)";
        }

        $marks = implode(', ', $sqlArray);
        return $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $allValues);
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
        unset($pdoArray['id']);

        $columns = implode(', ', array_keys($pdoArray));
        $values = implode(', ', array_fill(0, count($pdoArray), '?'));

        return [$columns, $values, array_values($pdoArray)];
    }

    #[Pure]
    private function primaryArrayToCriteria(array $primaries): Criteria
    {
        return new Criteria(
            array_combine(
                $this->primary,
                $primaries
            )
        );
    }

    public function readOne(array $primaries): mixed
    {
        return $this->readOneBy($this->primaryArrayToCriteria($primaries));
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

        $criteria = $this->primaryArrayToCriteria($row->getPrimary());

        return $this->requester->execute(
            "UPDATE {$this->table()} SET $sets " . Criteria::getWhere($criteria),
            array_merge(array_values($pdoArray), $criteria->getParams())
        );
    }

    public function deleteOne(PdoRowInterface $row): bool
    {
        $criteria = $this->primaryArrayToCriteria($row->getPrimary());
        return $this->requester->execute(
            "DELETE FROM {$this->table()} " . Criteria::getWhere($criteria),
            $criteria->getParams()
        );
    }

    public static function instantiateFromRow(string $className, array $row): mixed
    {
        $rowObj = new $className();
        $rowObj->fromPdoArray($row);
        return $rowObj;
    }

}
