<?php

namespace Thor\Database\PdoTable\PdoTable;

use ReflectionClass;
use ReflectionAttribute;
use ReflectionException;
use JetBrains\PhpStorm\Pure;
use JetBrains\PhpStorm\ArrayShape;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoTable;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoForeignKey;

/**
 * Class used to read PdoTable\Attributes of a class extending PdoRowInterface.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
final class PdoAttributesReader
{

    #[ArrayShape(['table' => PdoTable::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    private static array $classInfos = [];

    /**
     * @param class-string $classname
     */
    public function __construct(private string $classname)
    {
    }

    #[ArrayShape(['table' => PdoTable::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    private static function parseAttributes(
        ReflectionClass $rc
    ): array {
        /** @var \Thor\Database\PdoTable\PdoTable\Attributes\PdoTable $table */
        $table = ($rc->getAttributes(PdoTable::class)[0] ?? null)?->newInstance();
        $columns = array_map(
            fn(ReflectionAttribute $ra) => $ra->newInstance(),
            $rc->getAttributes(PdoColumn::class)
        );
        /** @var PdoIndex[] $indexes */
        $indexes = array_map(
            fn(ReflectionAttribute $ra) => $ra->newInstance(),
            $rc->getAttributes(PdoIndex::class)
        );
        /** @var \Thor\Database\PdoTable\PdoTable\Attributes\PdoForeignKey[] $fks */
        $fks = array_map(
            fn(ReflectionAttribute $ra) => $ra->newInstance(),
            $rc->getAttributes(PdoForeignKey::class)
        );

        foreach ($rc->getTraits() as $t) {
            ['table' => $pTable, 'columns' => $pColumns, 'indexes' => $pIndexes, 'foreign_keys' => $pFks] =
                self::parseAttributes($t);
            ['table' => $table, 'columns' => $columns, 'indexes' => $indexes] =
                self::_merge($pTable, $table, $pColumns, $columns, $pIndexes, $indexes, $fks, $pFks);
        }

        if ($p = $rc->getParentClass()) {
            ['table' => $pTable, 'columns' => $pColumns, 'indexes' => $pIndexes, 'foreign_keys' => $pFks] =
                self::parseAttributes($p);
            ['table' => $table, 'columns' => $columns, 'indexes' => $indexes] =
                self::_merge($pTable, $table, $pColumns, $columns, $pIndexes, $indexes, $fks, $pFks);
        }

        return ['table' => $table, 'columns' => $columns, 'indexes' => $indexes, 'foreign_keys' => $fks];
    }

    #[Pure]
    #[ArrayShape(['table' => PdoTable::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    private static function _merge(
        ?PdoTable $tableA,
        ?PdoTable $tableB,
        array $columnsA,
        array $columnsB,
        array $indexA,
        array $indexB,
        array $fkA,
        array $fkB
    ): array {
        return [
            'table' => ($tableA === null) ? $tableB :
                new PdoTable(
                    $tableB?->getTableName() ?? $tableA->getTableName(),
                    array_merge($tableA->getPrimaryKeys(), $tableB?->getPrimaryKeys() ?? []),
                    $tableB?->getAutoColumnName() ?? $tableA->getAutoColumnName(),
                )
            ,
            'columns' => array_merge($columnsA, $columnsB),
            'indexes' => array_merge($indexA, $indexB),
            'foreign_keys' => array_merge($fkA, $fkB)
        ];
    }

    /**
     * Returns attributes of the read class.
     *
     * @throws ReflectionException
     */
    #[ArrayShape(['table' => PdoTable::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    public function getAttributes(): array
    {
        return self::$classInfos[$this->classname] ??= self::parseAttributes(new ReflectionClass($this->classname));
    }

    /**
     * Returns attributes of the specified class.
     *
     * @param class-string $className
     *
     * @throws ReflectionException
     */
    #[ArrayShape(['table' => PdoTable::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    public static function pdoTableInformation(string $className): array
    {
        return (new self($className))->getAttributes();
    }

}

