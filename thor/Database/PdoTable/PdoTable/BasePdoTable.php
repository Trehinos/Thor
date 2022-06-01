<?php

namespace Thor\Database\PdoTable\PdoTable;

use Thor\Database\PdoTable\HasId;
use Thor\Database\PdoTable\HasIdTrait;

/**
 * Default implementor of PdoRowInterface.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
abstract class BasePdoTable implements PdoRowInterface, HasId
{

    use PdoRowTrait {
        PdoRowTrait::__construct as private traitConstructor;
    }
    use HasIdTrait;

    /**
     * @param int|null $id
     */
    public function __construct(?int $id = null)
    {
        $this->traitConstructor(['id' => $id]);
    }

}
