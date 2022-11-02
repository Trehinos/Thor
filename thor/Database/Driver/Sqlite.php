<?php

namespace Thor\Database\Driver;

use Thor\Database\Definition\Attributes\C;
use Thor\Database\Definition\Attributes\I;
use Thor\Database\Definition\Attributes\T;
use Thor\Database\PdoTable\PdoTable\PdoAttributesReader;

/**
 *
 */

/**
 *
 */
class Sqlite implements DriverInterface
{

    private string $tableName = '';

    /**
     * @param string $className
     *
     * @return string
     * @throws \ReflectionException
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
                fn(C $column) => $this->addColumn($column, $autoKey),
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

    /**
     * @param C   $column
     * @param string|null $autoKey
     *
     * @return string
     */
    public function addColumn(C $column, ?string $autoKey = null): string
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

    /**
     * @param T    $table
     * @param string|null $autoKey
     *
     * @return string
     */
    public function primaryKeys(T $table, ?string $autoKey = null): string
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

    /**
     * @param string $className
     *
     * @return array
     * @throws \ReflectionException
     */
    public function createIndexes(string $className): array
    {
        $attrs = new PdoAttributesReader($className);
        $this->tableName = $attrs->getAttributes()['table']->getTableName();
        return array_map(
            fn(I $index) => $this->addIndex($index),
            $attrs->getAttributes()['indexes']
        );
    }

    /**
     * @param I $index
     *
     * @return string
     */
    public function addIndex(I $index): string
    {
        $unq = $index->isUnique() ? ' UNIQUE' : '';
        $cols = implode(', ', $index->getColumnNames());

        return "CREATE $unq INDEX {$index->getName()} ON {$this->tableName}($cols)";
    }
}
