<?php

namespace Thor\Database\PdoTable\Driver;

use ReflectionException;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoTable;
use Thor\Database\PdoTable\PdoTable\PdoAttributesReader;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoColumn;

class MySql implements DriverInterface
{

    /**
     * @param class-string $className
     *
     * @throws ReflectionException
     */
    public function createTable(string $className): string
    {
        $attrs = new PdoAttributesReader($className);
        $separator = ",\n    ";
        $tableName = $attrs->getAttributes()['table']->getTableName();
        $autoKey = $attrs->getAttributes()['table']->getAutoColumnName();
        $columns = implode(
            $separator,
            array_map(
                fn(PdoColumn $column) => $this->addColumn($column, $autoKey),
                $attrs->getAttributes()['columns']
            )
        );
        $indexes = implode(
            $separator,
            array_map(
                fn(PdoIndex $index) => $this->addIndex($index),
                $attrs->getAttributes()['indexes']
            )
        );
        $primary = $this->primaryKeys($attrs->getAttributes()['table'], $autoKey);

        if ($indexes !== '') {
            $indexes = "$separator$indexes";
        }
        if ($primary !== '') {
            $primary = "$separator$primary";
        }

        return <<<§
            CREATE TABLE $tableName (
                $columns$primary$indexes
            )
            §;
    }

    public function addColumn(PdoColumn $column, ?string $autoKey = null): string
    {
        $nullStr = $column->isNullable() ? '' : ' NOT NULL';
        $defaultStr = ($column->getDefault() === null)
            ? ($column->isNullable() ? ' DEFAULT NULL' : '')
            : " DEFAULT {$column->getDefault()}";

        return "{$column->getName()} {$column->getSqlType()}$nullStr$defaultStr" .
               (($column->getName() === $autoKey) ? ' AUTO_INCREMENT' : '');
    }

    public function addIndex(PdoIndex $index): string
    {
        $unq = $index->isUnique() ? ' UNIQUE' : '';
        $cols = implode(', ', $index->getColumnNames());

        return "CONSTRAINT$unq INDEX {$index->getName()} ($cols)";
    }

    public function primaryKeys(PdoTable $table, ?string $autoKey = null): string
    {
        $keys = $table->getPrimaryKeys();
        if (empty($keys)) {
            return '';
        }
        $primary = implode(', ', $keys);
        return "PRIMARY KEY ($primary)";
    }

    public function createIndexes(string $className): array
    {
        return [];
    }
}
