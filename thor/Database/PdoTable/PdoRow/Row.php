<?php

namespace Thor\Database\PdoTable\PdoRow;

/**
 * Default implementor of PdoRowInterface.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
abstract class Row implements RowInterface
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
