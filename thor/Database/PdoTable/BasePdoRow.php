<?php

namespace Thor\Database\PdoTable;

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

    public function __construct(array $primaries = [])
    {
        $this->traitConstructor($primaries);
    }

}
