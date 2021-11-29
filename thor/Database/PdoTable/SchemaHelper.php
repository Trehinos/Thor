<?php

namespace Thor\Database\PdoTable;

use ReflectionException;
use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoTable\{Attributes\PdoIndex, Attributes\PdoColumn, Attributes\PdoAttributesReader};

/**
 * This class provides methods to execute DQL statements from a PdoAttributesReader.
 *
 * @package   Thor\Database\PdoTable
 *
 * @template T
 * @since     2020-10
 * @version   1.0
 * @author    Trehinos
 * @copyright Author
 * @license   MIT
 */
final class SchemaHelper
{

    /**
     * @param PdoRequester        $requester
     * @param PdoAttributesReader $reader
     * @param bool                $isDebug if true, generates and returns SQL statements instead of executing them.
     */
    public function __construct(
        private PdoRequester $requester,
        private PdoAttributesReader $reader,
        private bool $isDebug = false
    ) {
    }

    /**
     * Create the table in the database.
     *
     * @throws ReflectionException
     */
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

    /**
     * Drop the table in the database.
     *
     * @throws ReflectionException
     */
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
