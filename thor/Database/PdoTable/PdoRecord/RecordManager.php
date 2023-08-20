<?php

namespace Thor\Database\PdoTable\PdoRecord;

use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoTable\Driver\DriverInterface;
use Thor\Database\PdoTable\PdoRow\PdoRowInterface;
use Thor\Database\PdoTable\SchemaHelper;

final readonly class RecordManager
{

    public function __construct(public CrudHelper $crud, public  SchemaHelper $schema) {}

    /**
     * @template T
     * @template-implements PdoRowInterface
     * @template-implements PdoRecordInterface
     *
     * @param DriverInterface $driver
     * @param PdoRequester $requester
     * @param class-string<T> $class
     *
     * @return PdoRecordInterface
     */
    public static function create(DriverInterface $driver, PdoRequester $requester, string $class): object
    {
        return new $class(
            new CrudHelper($class, $requester),
            new SchemaHelper($requester, $driver, $class)
        );
    }

}
