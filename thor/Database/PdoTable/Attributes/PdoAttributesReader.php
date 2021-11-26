<?php

namespace Thor\Database\PdoTable\Attributes;

use ReflectionException;
use JetBrains\PhpStorm\ArrayShape;
use ReflectionAttribute;
use ReflectionClass;

/**
 * Class used to read PdoTable\Attributes of a class extending PdoRowInterface.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
final class PdoAttributesReader
{

    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    private static array $classInfos = [];

    /**
     * @param class-string $classname
     */
    public function __construct(private string $classname)
    {
    }

    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    private static function parseAttributes(
        ReflectionClass $rc
    ): array {
        /** @var PdoRow $row */
        $row = ($rc->getAttributes(PdoRow::class)[0] ?? null)?->newInstance();
        $columns = array_map(
            fn(ReflectionAttribute $ra) => $ra->newInstance(),
            $rc->getAttributes(PdoColumn::class)
        );
        /** @var PdoIndex[] $indexes */
        $indexes = array_map(
            fn(ReflectionAttribute $ra) => $ra->newInstance(),
            $rc->getAttributes(PdoIndex::class)
        );
        /** @var PdoForeignKey[] $fks */
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

    /**
     * Returns attributes of the read class.
     *
     * @throws ReflectionException
     */
    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
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
    #[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])]
    public static function pdoRowInfo(string $className): array
    {
        return (new self($className))->getAttributes();
    }

}

