<?php

namespace Thor\Database\PdoTable\PdoRecord;

use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Database\PdoTable\Driver\DriverInterface;
use Thor\Database\PdoTable\PdoRow\PdoRowInterface;
use Thor\Database\PdoTable\PdoRow\PdoRowTrait;
use Thor\Database\PdoTable\SchemaHelper;

class BasePdoRecord implements PdoRowInterface, PdoRecord
{
    use PdoRowTrait {
        PdoRowTrait::__construct as private pdoRow;
    }
    use PdoRecordTrait {
        PdoRecordTrait::__construct as private pdoRecord;
    }

    public function __construct(CrudHelper $crudHelper, SchemaHelper $schemaHelper, array $primaries)
    {
        $this->pdoRow($primaries);
        $this->pdoRecord($crudHelper, $schemaHelper);
        $this->empty = empty($primaries);
        $this->reload();
    }

    public static function load(DriverInterface $driver, PdoRequester $requester, array $primaries): self
    {
        return new self(
            new CrudHelper(self::class, $requester),
            new SchemaHelper($requester, $driver, self::class),
            $primaries
        );
    }
}
