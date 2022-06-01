<?php

namespace Thor\Database\PdoExtension;

use JetBrains\PhpStorm\Pure;
use Thor\Database\PdoTable\Criteria;

/**
 * For class CrudHelper : SQL CRUD operation requester for PdoRows.
 *
 * @package Thor\Database\Sql
 */
final class PdoArrayCrud
{

    /**
     * @param string       $tableName
     * @param array        $primary
     * @param PdoRequester $requester
     */
    public function __construct(
        private string $tableName,
        private array $primary,
        private PdoRequester $requester
    ) {
    }

    /**
     * @return array
     */
    public function listAll(): array
    {
        return $this->requester->request("SELECT * FROM {$this->table()}", [])->fetchAll();
    }

    /**
     * @return string
     */
    #[Pure]
    public function table(): string
    {
        return $this->tableName;
    }

    /**
     * @param array $row
     *
     * @return string
     */
    public function createOne(array $row): string
    {
        [$columns, $marks, $values] = self::compileRowValues($row);
        $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $values);

        return $this->getPrimaryString($row);
    }

    /**
     * @param array $row
     *
     * @return string
     */
    private function getPrimaryString(array $row): string
    {
        return implode('_', $this->extractPrimaries($row));
    }

    /**
     * @param array $row
     *
     * @return array
     */
    private function extractPrimaries(array $row): array
    {
        $pkeyParts = [];
        foreach ($this->primary as $pkey) {
            if (array_key_exists($pkey, $row)) {
                $pkeyParts[$pkey] = $row[$pkey];
            }
        }

        return $pkeyParts;
    }

    /**
     * @param array $row
     *
     * @return array
     */
    private static function compileRowValues(array $row): array
    {
        $pdoArray = $row;
        unset($pdoArray['id']);

        $columns = implode(', ', array_keys($pdoArray));
        $values = implode(', ', array_fill(0, count($pdoArray), '?'));

        return [$columns, $values, array_values($pdoArray)];
    }

    /**
     * @param array[] $rows
     *
     * @return bool
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
        return $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES $marks", $allValues);
    }

    /**
     * @param array $primaries
     *
     * @return array|null
     */
    public function readOne(array $primaries): ?array
    {
        return $this->readOneBy($this->primaryArrayToCriteria($primaries));
    }

    /**
     * @param Criteria          $criteria
     * @param array|string|null $columns
     *
     * @return array|null
     */
    public function read(Criteria $criteria, array|string|null $columns = null): ?array
    {
        $columnSql = match (true) {
            is_null($columns)   => '*',
            is_string($columns) => "$columns",
            is_array($columns)  => implode(
                ', ',
                array_map(
                    fn(string $column, ?string $alias = null) => $alias ? "$column as $alias" : $column,
                    array_values($columns),
                    array_keys($columns)
                )
            )
        };

        $sql = Criteria::getWhere($criteria);
        $row = $this->requester->request(
                "SELECT $columnSql FROM {$this->table()} $sql",
                $criteria->getParams()
            )->fetch() ?? [];

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * @param Criteria $criteria
     *
     * @return array|null
     */
    public function readOneBy(Criteria $criteria): ?array
    {
        return $this->read($criteria);
    }

    /**
     * @param array $primaries
     *
     * @return Criteria
     */
    private function primaryArrayToCriteria(array $primaries): Criteria
    {
        $criteria = [];
        foreach ($this->primary as $primaryKey) {
            $criteria[$primaryKey] = array_shift($primaries);
        }

        return new Criteria($criteria);
    }


    /**
     * @param string $pid
     *
     * @return array|null
     */
    public function readOneFromPid(string $pid): ?array
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
        return $this->requester->request(
                $sql,
                $criteria->getParams()
            )->fetchAll() ?? [];
    }

    /**
     * @param array $row
     *
     * @return bool
     */
    public function updateOne(array $row): bool
    {
        $sets = implode(', ', array_map(fn(string $col) => "$col = ?", array_keys($row)));

        $criteria = $this->primaryArrayToCriteria($this->extractPrimaries($row));

        return $this->requester->execute(
            "UPDATE {$this->table()} SET $sets " . Criteria::getWhere($criteria),
            array_merge(array_values($row), $criteria->getParams())
        );
    }

    /**
     * @param array $row
     *
     * @return bool
     */
    public function deleteOne(array $row): bool
    {
        $criteria = $this->primaryArrayToCriteria($this->extractPrimaries($row));
        return $this->requester->execute(
            "DELETE FROM {$this->table()} " . Criteria::getWhere($criteria),
            $criteria->getParams()
        );
    }

}
