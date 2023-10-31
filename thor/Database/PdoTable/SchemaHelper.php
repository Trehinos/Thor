<?php

namespace Thor\Database\PdoTable;

use ReflectionException;
use Thor\Database\PdoExtension\Requester;
use Thor\Database\PdoTable\{Driver\DriverInterface, PdoRow\Attributes\Index, PdoRow\AttributesReader};

/**
 * This class provides methods to execute DQL statements from a PdoAttributesReader.
 *
 * @package   Thor\Database\PdoTable
 *
 * @template  T
 * @since     2020-10
 * @version   1.0
 * @author    Trehinos
 * @copyright Author
 * @license   MIT
 */
final class SchemaHelper
{

    /**
     * @param Requester    $requester
     * @param DriverInterface $driver
     * @param class-string    $className
     * @param bool            $isDebug if true, generates and returns SQL statements instead of executing them.
     */
    public function __construct(
        private Requester $requester,
        private DriverInterface $driver,
        private string $className,
        private bool $isDebug = false
    ) {
    }

    /**
     * Create the table in the database.
     */
    public function createTable(): bool|string
    {
        $createTableSql = $this->driver->createTable($this->className);
        $sql = $createTableSql;

        $result = true;
        if (!$this->isDebug) {
            $result = $this->requester->execute($createTableSql);
        }

        $createIndexesSqls = $this->driver->createIndexes($this->className);
        if (!$this->isDebug) {
            foreach ($createIndexesSqls as $createIndexSql) {
                $result = $result && $this->requester->execute($createIndexSql);
            }
            return $result;
        } else {
            $sql .= ";\n" . implode(";\n", $createIndexesSqls);
        }

        return $sql;
    }

    /**
     * Drop the table in the database.
     *
     * @throws ReflectionException
     */
    public function dropTable(): bool|string
    {
        $attrs = (new AttributesReader($this->className))->getAttributes();
        $tableName = $attrs['table']->getTableName();

        $sql = '';

        $result = true;
        /** @var Index $index */
        foreach ($attrs['indexes'] as $index) {
            $sql_i = "DROP INDEX {$index->getName()} ON $tableName";
            if ($this->isDebug) {
                $sql .= $sql_i . "\n";
            }
            $result = $result && $this->requester->request($sql_i);
        }

        $sql_i = "DROP TABLE $tableName";
        if ($this->isDebug) {
            $sql .= $sql_i;
            return $sql;
        }
        return $result && $this->requester->execute($sql_i);
    }

}
