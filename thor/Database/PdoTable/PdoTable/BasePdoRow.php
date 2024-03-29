<?php

namespace Thor\Database\PdoTable\PdoTable;

/**
 * Default implementor of PdoRowInterface.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
abstract class BasePdoRow implements PdoRowInterface
{

    use PdoRowTrait {
        PdoRowTrait::__construct as private traitConstructor;
    }

    /**
     * @param array $primaries
     */
    public function __construct(array $primaries = [])
    {
        $this->traitConstructor($primaries);
    }

}
