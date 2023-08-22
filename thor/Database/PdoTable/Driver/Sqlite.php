<?php

namespace Thor\Database\PdoTable\Driver;

use Thor\Database\PdoTable\PdoRow\Attributes\Index;
use Thor\Database\PdoTable\PdoRow\Attributes\Table;
use Thor\Database\PdoTable\PdoRow\PdoAttributesReader;
use Thor\Database\PdoTable\PdoRow\Attributes\Column;

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
                fn(Column $column) => $this->addColumn($column, $autoKey),
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
     * @param Column   $column
     * @param string|null $autoKey
     *
     * @return string
     */
    public function addColumn(Column $column, ?string $autoKey = null): string
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
     * @param Table    $table
     * @param string|null $autoKey
     *
     * @return string
     */
    public function primaryKeys(Table $table, ?string $autoKey = null): string
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
            fn(Index $index) => $this->addIndex($index),
            $attrs->getAttributes()['indexes']
        );
    }

    /**
     * @param Index $index
     *
     * @return string
     */
    public function addIndex(Index $index): string
    {
        $unq = $index->isUnique() ? ' UNIQUE' : '';
        $cols = implode(', ', $index->getColumnNames());

        return "CREATE $unq INDEX {$index->getName()} ON {$this->tableName}($cols)";
    }
}
