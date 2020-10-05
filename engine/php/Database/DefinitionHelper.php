<?php

namespace Thor\Database;

final class DefinitionHelper
{

    private array $schema;

    public function __construct(array $configuration)
    {
        $this->schema = $configuration;
    }

    public function getDefinition(string $name): ?array
    {
        foreach ($this->schema as $definition) {
            if ($definition['name'] === $name) {
                return $definition;
            }
        }

        return null;
    }

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

            $tableDef['columns'] += ($extendsDef['columns'] ?? []);
            $tableDef['index'] += ($extendsDef['index'] ?? []);
        }

        return $tableDef;
    }

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
