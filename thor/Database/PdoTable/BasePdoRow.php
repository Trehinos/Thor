<?php

/**
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoTable;

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
