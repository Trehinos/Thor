<?php

/**
 * @package Trehinos/Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoTable;

use Thor\Database\PdoTable\Attributes\PdoAttributesReader;
use Thor\Database\PdoTable\Attributes\PdoColumn;
use Thor\Database\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoExtension\PdoRequester;

final class SchemaHelper
{

    public function __construct(
        private PdoRequester $requester,
        private PdoAttributesReader $reader,
        private bool $isDebug = false
    ) {
    }

    public function createTable(): bool|string
    {
        $separator = ",\n    ";
        $tableName = $this->reader->getAttributes()['row']->getTableName();
        $primary = implode(', ', $this->reader->getAttributes()['row']->getPrimaryKeys());
        $autoKey = $this->reader->getAttributes()['row']->getAutoColumnName();
        $columns = implode(
            $separator,
            array_map(
                fn(PdoColumn $column) => $column->getSql() .
                    (($column->getName() === $autoKey) ? ' AUTO_INCREMENT' : ''),
                $this->reader->getAttributes()['columns']
            )
        );
        $indexes = implode(
            $separator,
            array_map(
                fn(PdoIndex $index) => $index->getSql(),
                $this->reader->getAttributes()['indexes']
            )
        );

        if ($indexes !== '') {
            $indexes = "$separator$indexes";
        }

        $sql = <<<§
            CREATE TABLE $tableName (
                $columns,
                PRIMARY KEY ($primary)$indexes
            )
            §;

        if ($this->isDebug) {
            return $sql;
        }

        return $this->requester->execute($sql, []);
    }

    public function dropTable(): bool|string
    {
        $tableName = $this->reader->getAttributes()['row']->getTableName();

        $sql = '';

        $result = true;
        /** @var PdoIndex $index */
        foreach ($this->reader->getAttributes()['indexes'] as $index) {
            $sql_i = "DROP INDEX {$index->getName()} ON $tableName";
            if ($this->isDebug) {
                $sql .= $sql_i . "\n";
            }
            $result = $result && $this->requester->request($sql_i, []);
        }

        $sql_i = "DROP TABLE $tableName";
        if ($this->isDebug) {
            $sql .= $sql_i;
            return $sql;
        }
        return $result && $this->requester->execute($sql_i, []);
    }

}
