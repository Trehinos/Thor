<?php

namespace Thor\Database\Driver;

use ReflectionException;
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
                fn(C $column) => $this->addColumn($column, $autoKey),
                $attrs->getAttributes()['columns']
            )
        );
        $indexes = implode(
            $separator,
            array_map(
                fn(I $index) => $this->addIndex($index),
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
     * @param C   $column
     * @param string|null $autoKey
     *
     * @return string
     */
    public function addColumn(C $column, ?string $autoKey = null): string
    {
        $nullStr = $column->isNullable() ? '' : ' NOT NULL';
        $defaultStr = ($column->getDefault() === null)
            ? ($column->isNullable() ? ' DEFAULT NULL' : '')
            : " DEFAULT {$column->getDefault()}";

        return "{$column->getName()} {$column->getSqlType()}$nullStr$defaultStr" .
               (($column->getName() === $autoKey) ? ' AUTO_INCREMENT' : '');
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

        return "CONSTRAINT$unq INDEX {$index->getName()} ($cols)";
    }

    /**
     * @param T    $table
     * @param string|null $autoKey
     *
     * @return string
     */
    public function primaryKeys(T $table, ?string $autoKey = null): string
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
