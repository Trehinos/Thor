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

    public function __construct(
        private string $tableName,
        private array $primary,
        private PdoRequester $requester
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

    public function createOne(array $row): string
    {
        [$columns, $marks, $values] = self::compileRowValues($row);
        $this->requester->execute("INSERT INTO {$this->table()} ($columns) VALUES ($marks)", $values);

        return $this->getPrimaryString($row);
    }

    private function getPrimaryString(array $row): string
    {
        return implode('_', $this->extractPrimaries($row));
    }

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

    public function readOne(array $primaries): ?array
    {
        return $this->readOneBy($this->primaryArrayToCriteria($primaries));
    }

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

    public function readOneBy(Criteria $criteria): ?array
    {
        return $this->read($criteria);
    }

    private function primaryArrayToCriteria(array $primaries): Criteria
    {
        $criteria = [];
        foreach ($this->primary as $primaryKey) {
            $criteria[$primaryKey] = array_shift($primaries);
        }

        return new Criteria($criteria);
    }


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

    public function updateOne(array $row): bool
    {
        $sets = implode(', ', array_map(fn(string $col) => "$col = ?", array_keys($row)));

        $criteria = $this->primaryArrayToCriteria($this->extractPrimaries($row));

        return $this->requester->execute(
            "UPDATE {$this->table()} SET $sets " . Criteria::getWhere($criteria),
            array_merge(array_values($row), $criteria->getParams())
        );
    }

    public function deleteOne(array $row): bool
    {
        $criteria = $this->primaryArrayToCriteria($this->extractPrimaries($row));
        return $this->requester->execute(
            "DELETE FROM {$this->table()} " . Criteria::getWhere($criteria),
            $criteria->getParams()
        );
    }

}
