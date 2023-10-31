<?php

namespace Thor\Database\PdoTable\PdoRecord;

use Thor\Database\PdoExtension\Requester;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoTable\Driver\DriverInterface;
use Thor\Database\PdoTable\PdoRow\RowInterface;
use Thor\Database\PdoTable\SchemaHelper;

final readonly class RecordManager
{

    public function __construct(public CrudHelper $crud, public  SchemaHelper $schema) {}

    /**
     * @template T
     * @template-implements RowInterface
     * @template-implements RecordInterface
     *
     * @param DriverInterface $driver
     * @param Requester $requester
     * @param class-string<T> $class
     *
     * @return RecordInterface
     */
    public static function create(DriverInterface $driver, Requester $requester, string $class): object
    {
        return new $class(
            new CrudHelper($class, $requester),
            new SchemaHelper($requester, $driver, $class)
        );
    }

}
