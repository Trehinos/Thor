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

    public function createTable(string $name): bool
    {
        $sql = $this->definitions->getTableDefinitionSql($name);
        if (null === $sql) {
            return false;
        }

        return $this->requester->execute($sql, []);
    }

    public function dropTable(string $name): bool
    {
        return $this->requester->execute("DROP TABLE $name", []);
    }

}
