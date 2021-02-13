<?php

namespace Thor\Database;

use Thor\Database\PdoExtension\Attributes\PdoAttributesReader;
use Thor\Database\PdoExtension\Attributes\PdoColumn;
use Thor\Database\PdoExtension\Attributes\PdoIndex;
use Thor\Database\PdoExtension\PdoRequester;

final class SchemaHelper
{

    public function __construct(private PdoRequester $requester, private PdoAttributesReader $reader)
    {
    }

    public function createTable(): bool
    {
        $tableName = $this->reader->getAttributes()['row']->getTableName();
        $primary = implode(', ', $this->reader->getAttributes()['row']->getPrimaryKeys());
        $autoKey = $this->reader->getAttributes()['row']->getAutoColumnName();
        $columns = implode(
            ', ',
            array_map(
                fn(PdoColumn $column) => $column->getSql() .
                    (($column->getName() === $autoKey) ? ' AUTO_INCREMENT' : ''),
                $this->reader->getAttributes()['columns']
            )
        );

        $result = $this->requester->execute(
            $sql = <<<§
                CREATE TABLE $tableName (
                    $columns,
                    PRIMARY KEY ($primary)
                )
                §,
            []
        );

        /** @var PdoIndex $index */
        foreach ($this->reader->getAttributes()['indexes'] as $index) {
            $result = $result && $this->requester->request($index->getSql($tableName), []);
        }

        return $result;
    }

    public function dropTable(): bool
    {
        $tableName = $this->reader->getAttributes()['row']->getTableName();

        $result = true;
        /** @var PdoIndex $index */
        foreach ($this->reader->getAttributes()['indexes'] as $index) {
            $result = $result && $this->requester->request("DROP INDEX {$index->getName()} ON $tableName", []);
        }


        return $result && $this->requester->execute("DROP TABLE $tableName", []);
    }

}
