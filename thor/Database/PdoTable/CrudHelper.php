<?php

/**
 * @package          Thor/Database/PdoTable
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
 * @template T
 * @package  Thor\Database\PdoTable
 */
final class CrudHelper implements CrudInterface
{

    private string $tableName;
    private array $primary;

    /**
     * CrudHelper constructor.
     * Creates a new CRUD requester to manage PdoRows
     *
     * @param class-string<T> $className which implements PdoRowInterface and use PdoRowTrait trait.
     */
    public function __construct(
        private string $className,
        private PdoRequester $requester,
        private array $excludeColumnsFromInsert = [],
        private array $excludeColumnsFromUpdate = [],
    ) {
        if (!class_exists($this->className) || !in_array(PdoRowInterface::class, class_implements($this->className))) {
            throw new TypeError("{$this->className} class not found or not implementing PdoRowInterface...");
        }
        $this->tableName = ($this->className)::getPdoTable()->getTableName();
        $this->primary = ($this->className)::getPrimaryKeys();
    }

    /**
     * List all rows of the entity managed by this Crud.
     *
     * @return T[]
     */
    public function listAll(): array
    {
        $rows = $this->requester->request("SELECT * FROM {$this->table()}", [])->fetchAll();
        if (empty($rows)) {
            return [];
        }

        $rowsObjs = [];
        foreach ($rows as $row) {
            $rowObj = self::instantiateFromRow($this->className, $row, true);
            $rowsObjs[] = $rowObj;
        }

        return $rowsObjs;
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function table(): string
    {
        return $this->tableName;
    }

    /**
     * @param class-string<T> $className
     *
     * @return T
     */
    public static function instantiateFromRow(
        string $className,
        array $row,
        bool $fromDb = false,
        mixed ...$constructorArguments
    ): object {
        $rowObj = new $className(...$constructorArguments);
        $rowObj->fromPdoArray($row, $fromDb);
        return $rowObj;
    }

    /**
     * Create one row in DB.
     *
     * @param T $row
     *
     * @return string primary string/key
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
        [$columns, $marks, $values] = self::compileRowValues($row, $this->excludeColumnsFromInsert);
        $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $values);

        if ($row instanceof AbstractPdoRow) {
            return $row->getPublicId();
        }

        return $row->getPrimaryString();
    }

    /**
     * @param T $row
     *
     * @throws Exception
     */
    private static function compileRowValues(PdoRowInterface $row, array $excludeColumns = []): array
    {
        if ($row instanceof AbstractPdoRow) {
            $row->generatePublicId();
        }
        $pdoArray = $row->toPdoArray();

        if (!empty($excludeColumns)) {
            $pdo = [];
            foreach ($pdoArray as $column => $value) {
                if (!in_array($column, $excludeColumns)) {
                    $pdo[$column] = $value;
                }
            }
            $pdoArray = $pdo;
        }

        $columns = implode(', ', array_keys($pdoArray));
        $values = implode(', ', array_fill(0, count($pdoArray), '?'));

        return [$columns, $values, array_values($pdoArray)];
    }

    /**
     * Creates multiple rows in DB.
     *
     * @param T[] $rows
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
            [$columns, $marks, $values] = self::compileRowValues($row, $this->excludeColumnsFromInsert);

            $allValues = array_merge($allValues, $values);
            $sqlArray [] = "($marks)";
        }

        $marks = implode(', ', $sqlArray);
        return $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $allValues);
    }

    /**
     * Gets one row from its primary keys.
     *
     * Returns `null` if the row has not been found.
     *
     * @param array $primaries
     *
     * @return T|null
     */
    public function readOne(array $primaries): ?PdoRowInterface
    {
        return $this->readOneBy($this->primaryArrayToCriteria($primaries));
    }

    /**
     * Gets one row from the specified criteria.
     *
     * Returns `null` if the row has not been found.
     *
     * @param Criteria $criteria
     *
     * @return T|null
     */
    public function readOneBy(Criteria $criteria): ?PdoRowInterface
    {
        $sql = Criteria::getWhere($criteria);
        $row = $this->requester->request(
                "SELECT * FROM {$this->table()} $sql",
                $criteria->getParams()
            )->fetchAll()[0] ?? [];

        if (empty($row)) {
            return null;
        }

        return self::instantiateFromRow($this->className, $row, true);
    }

    private function primaryArrayToCriteria(array $primaries): Criteria
    {
        $criteria = [];
        foreach ($this->primary as $primaryKey) {
            $criteria[$primaryKey] = array_shift($primaries);
        }

        return new Criteria($criteria);
    }

    /**
     * Gets multiple rows from the specified criteria.
     *
     * @param Criteria $criteria
     *
     * @return T[]
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
            $objs[] = self::instantiateFromRow($this->className, $row, true);
        }

        return $objs;
    }

    /**
     * Updates all column of the corresponding row in DB.
     *
     * Returns true if the statement has correctly been executed.
     *
     * @param T $row
     *
     * @return bool
     */
    public function updateOne(PdoRowInterface $row): bool
    {
        $pdoArray = $row->toPdoArray();
        if (!empty($this->excludeColumnsFromUpdate)) {
            $pdoTmp = [];
            foreach ($pdoArray as $key => $item) {
                if (!in_array($key, $this->excludeColumnsFromUpdate)) {
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

    /**
     * Delete the corresponding row in DB.
     *
     * Returns true if the statement has correctly been executed.
     *
     * @param T $row
     *
     * @return bool
     */
    public function deleteOne(PdoRowInterface $row): bool
    {
        $criteria = $this->primaryArrayToCriteria($row->getFormerPrimary());
        return $this->requester->execute(
            "DELETE FROM {$this->table()} " . Criteria::getWhere($criteria),
            $criteria->getParams()
        );
    }

}
