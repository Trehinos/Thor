<?php

namespace Thor\Database;

use Thor\Database\PdoExtension\PdoRequester;

final class SchemaHelper
{

    private PdoRequester $requester;

    private DefinitionHelper $definitions;

    public function __construct(PdoRequester $requester, DefinitionHelper $definitions)
    {
        $this->requester = $requester;
        $this->definitions = $definitions;
    }

    /**
     * createTable(): create the named table in the database with the SchemaHelper's DefinitionHelper ...help =P
     *
     * @param string $name
     *
     * @return bool
     */
    public function createTable(string $name): bool
    {
        $sql = $this->definitions->getTableDefinitionSql($name);
        if (null === $sql) {
            return false;
        }

        return $this->requester->execute($sql, []);
    }

    /**
     * dropTable(): delete the named table from the database.
     *
     * @param string $name
     *
     * @return bool
     */
    public function dropTable(string $name): bool
    {
        return $this->requester->execute("DROP TABLE $name", []);
    }

}
