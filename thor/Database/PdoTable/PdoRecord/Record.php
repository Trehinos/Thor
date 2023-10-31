<?php

namespace Thor\Database\PdoTable\PdoRecord;

use Thor\Database\PdoTable\PdoRow\PdoRowTrait;

class Record implements RecordInterface
{
    use PdoRowTrait {
        PdoRowTrait::__construct as private pdoRow;
    }
    use RecordTrait {
        RecordTrait::__construct as private pdoRecord;
    }

    public function __construct(RecordManager $manager, array $primaries)
    {
        $this->pdoRow($primaries);
        $this->pdoRecord($manager);
        $this->objectEmpty = empty($primaries);
        $this->reload();
    }

}
