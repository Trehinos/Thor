<?php

namespace Thor\Database\PdoTable\Driver;

use Thor\Database\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\Attributes\PdoTable;
use Thor\Database\PdoTable\Attributes\PdoColumn;
use Thor\Database\PdoTable\Attributes\PdoAttributesReader;

class Sqlite implements DriverInterface
{

    private string $tableName = '';

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
        $primary = $this->primaryKeys($attrs->getAttributes()['table'], $autoKey);

        if ($primary !== '') {
            $primary = "$separator$primary";
        }

        return <<<§
            CREATE TABLE $tableName (
                $columns$primary
            )
            §;
    }

    public function addColumn(PdoColumn $column, ?string $autoKey = null): string
    {
        if ($autoKey === $column->getName()) {
            return "{$column->getName()} INTEGER PRIMARY KEY AUTOINCREMENT";
        }
        $nullStr = $column->isNullable() ? '' : ' NOT NULL';
        $defaultStr = ($column->getDefault() === null)
            ? ($column->isNullable() ? ' DEFAULT NULL' : '')
            : " DEFAULT {$column->getDefault()}";

        return "{$column->getName()} {$column->getSqlType()}$nullStr$defaultStr";
    }

    public function primaryKeys(PdoTable $table, ?string $autoKey = null): string
    {
        if ($autoKey !== null) {
            return '';
        }
        $keys = $table->getPrimaryKeys();
        if (empty($keys)) {
            return '';
        }
        $primary = implode(', ', $keys);
        return "PRIMARY KEY ($primary)";
    }

    public function createIndexes(string $className): array
    {
        $attrs = new PdoAttributesReader($className);
        $this->tableName = $attrs->getAttributes()['table']->getTableName();
        return array_map(
            fn(PdoIndex $index) => $this->addIndex($index),
            $attrs->getAttributes()['indexes']
        );
    }

    public function addIndex(PdoIndex $index): string
    {
        $unq = $index->isUnique() ? ' UNIQUE' : '';
        $cols = implode(', ', $index->getColumnNames());

        return "CREATE $unq INDEX {$index->getName()} ON {$this->tableName}($cols)";
    }
}
