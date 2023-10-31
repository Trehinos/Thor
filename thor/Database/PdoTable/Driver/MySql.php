<?php

namespace Thor\Database\PdoTable\Driver;

use ReflectionException;
use Thor\Database\PdoTable\PdoRow\Attributes\Index;
use Thor\Database\PdoTable\PdoRow\Attributes\Table;
use Thor\Database\PdoTable\PdoRow\AttributesReader;
use Thor\Database\PdoTable\PdoRow\Attributes\Column;

/**
 *
 */

/**
 *
 */
class MySql implements DriverInterface
{

    /**
     * @param class-string $className
     *
     * @throws ReflectionException
     */
    public function createTable(string $className): string
    {
        $attrs = new AttributesReader($className);
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
        $indexes = implode(
            $separator,
            array_map(
                fn(Index $index) => $this->addIndex($index),
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

    /**
     * @param Column   $column
     * @param string|null $autoKey
     *
     * @return string
     */
    public function addColumn(Column $column, ?string $autoKey = null): string
    {
        $nullStr = $column->isNullable() ? '' : ' NOT NULL';
        $defaultStr = ($column->getDefault() === null)
            ? ($column->isNullable() ? ' DEFAULT NULL' : '')
            : " DEFAULT {$column->getDefault()}";

        return "{$column->getName()} {$column->getSqlType()}$nullStr$defaultStr" .
               (($column->getName() === $autoKey) ? ' AUTO_INCREMENT' : '');
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

        return "CONSTRAINT$unq INDEX {$index->getName()} ($cols)";
    }

    /**
     * @param Table    $table
     * @param string|null $autoKey
     *
     * @return string
     */
    public function primaryKeys(Table $table, ?string $autoKey = null): string
    {
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
     */
    public function createIndexes(string $className): array
    {
        return [];
    }
}
