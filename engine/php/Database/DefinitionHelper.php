<?php

namespace Thor\Database;

final class DefinitionHelper
{

    /**
     * @var array the array definition representation.
     */
    private array $schema;

    public function __construct(array $configuration)
    {
        $this->schema = $configuration;
    }

    /**
     * getDefinition(): returns an array representation of a table (without table inheritance).
     *
     * @param string $name
     *
     * @return array|null
     */
    public function getDefinition(string $name): ?array
    {
        foreach ($this->schema as $definition) {
            if ($definition['name'] === $name) {
                return $definition;
            }
        }

        return null;
    }

    /**
     * getTableDefinition(): returns an array representation of a table (with table inheritance).
     *
     * @param string $name
     *
     * @return array|null
     */
    public function getTableDefinition(string $name): ?array
    {
        $tableDef = $this->getDefinition($name);
        if (null === $tableDef) {
            return null;
        }
        
        $extends = $tableDef['extends'] ?? null;
        if (null !== $extends) {
            $extendsDef = $this->getTableDefinition($extends);
            if (null === $extendsDef) {
                return null;
            }
            $tableDef['columns'] ??= [];
            $tableDef['index'] ??= [];

            $tableDef['columns'] = ($extendsDef['columns'] ?? []) + $tableDef['columns'] ;
            $tableDef['index'] = ($extendsDef['index'] ?? []) + $tableDef['index'];
        }

        return $tableDef;
    }

    /**
     * getTableDefinitionSql(): returns a create statement SQL code for the named table.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getTableDefinitionSql(string $name): ?string
    {
        $tableDefinition = $this->getTableDefinition($name);
        if (null === $tableDefinition) {
            return null;
        }

        $rows = [];
        foreach ($tableDefinition['columns'] as $cName => $def) {
            $rows[] = "$cName AS $def";
        }

        $pks = [];
        $unq = [];
        $idx = [];
        foreach ($tableDefinition['index'] as $indexName => $def) {
            switch (strtoupper($def)) {
                case 'PRIMARY':
                    $pks[] = $indexName;
                    break;
                case 'UNIQUE':
                    $unq[] = $indexName;
                    break;
                case 'INDEX':
                    $idx[] = $indexName;
                    break;
            }
        }

        if (!empty($pks)) {
            $rows[] = 'PRIMARY KEY (' . implode(', ', $pks) . ')';
        }
        foreach ($unq as $unqName) {
            $rows[] = "CONSTRAINT UNIQUE ($unqName)";
        }
        if (!empty($idx)) {
            $rows[] = 'INDEX (' . implode(', ', $idx) . ')';
        }

        $rowsStr = implode(', ', $rows);
        return "CREATE TABLE $name ($rowsStr)";
    }

    /**
     * getTableList(): returns all names of real tables in the definition array.
     *
     * @return array
     */
    public function getTablesList(): array
    {
        $tableNames = [];
        foreach ($this->schema as $definition) {
            $name = $definition['name'];
            $type = $definition['type'];
            if ('table' === $type) {
                $tableNames[] = $name;
            }
        }

        $tables = [];
        foreach ($tableNames as $tableName) {
            $tables[] = $this->getTableDefinition($tableName);
        }

        return $tables;
    }

}
