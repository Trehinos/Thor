<?php

/**
 * @package Trehinos/Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoTable\Attributes;

use JetBrains\PhpStorm\ArrayShape;
use ReflectionAttribute;
use ReflectionClass;

final class PdoAttributesReader
{

    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    private static array $classInfos = [];

    public function __construct(private string $classname)
    {
    }

    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    private static function parseAttributes(
        ReflectionClass $rc
    ): array {
        $row = ($rc->getAttributes(PdoRow::class)[0] ?? null)?->newInstance();
        $columns = array_map(
            fn(ReflectionAttribute $ra) => $ra->newInstance(),
            $rc->getAttributes(PdoColumn::class)
        );
        $indexes = array_map(
            fn(ReflectionAttribute $ra) => $ra->newInstance(),
            $rc->getAttributes(PdoIndex::class)
        );
        $fks = array_map(
            fn(ReflectionAttribute $ra) => $ra->newInstance(),
            $rc->getAttributes(PdoForeignKey::class)
        );

        foreach ($rc->getTraits() as $t) {
            ['row' => $pRow, 'columns' => $pColumns, 'indexes' => $pIndexes, 'foreign_keys' => $pFks] =
                self::parseAttributes($t);
            ['row' => $row, 'columns' => $columns, 'indexes' => $indexes] =
                self::_merge($pRow, $row, $pColumns, $columns, $pIndexes, $indexes, $fks, $pFks);
        }

        if ($p = $rc->getParentClass()) {
            ['row' => $pRow, 'columns' => $pColumns, 'indexes' => $pIndexes, 'foreign_keys' => $pFks] =
                self::parseAttributes($p);
            ['row' => $row, 'columns' => $columns, 'indexes' => $indexes] =
                self::_merge($pRow, $row, $pColumns, $columns, $pIndexes, $indexes, $fks, $pFks);
        }

        return ['row' => $row, 'columns' => $columns, 'indexes' => $indexes, 'foreign_keys' => $fks];
    }

    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    private static function _merge(
        ?PdoRow $rowA,
        ?PdoRow $rowB,
        array $columnsA,
        array $columnsB,
        array $indexA,
        array $indexB,
        array $fkA,
        array $fkB
    ): array {
        return [
            'row' => ($rowA === null) ? $rowB :
                new PdoRow(
                    $rowB?->getTableName() ?? $rowA->getTableName(),
                    array_merge($rowA->getPrimaryKeys(), $rowB?->getPrimaryKeys() ?? []),
                    $rowB?->getAutoColumnName() ?? $rowA->getAutoColumnName(),
                )
            ,
            'columns' => array_merge($columnsA, $columnsB),
            'indexes' => array_merge($indexA, $indexB),
            'foreign_keys' => array_merge($fkA, $fkB)
        ];
    }

    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    public function getAttributes(): array
    {
        return self::$classInfos[$this->classname] ??= self::parseAttributes(new ReflectionClass($this->classname));
    }

    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    public static function pdoRowInfo(string $className): array
    {
        return (new self($className))->getAttributes();
    }

}

