<?php

namespace Thor\Database\PdoTable;

use Exception;
use Thor\Database\PdoTable\{TableType\IntegerType, PdoRow\Attributes\Index, PdoRow\Attributes\Column};

/**
 * Adds an "id" column to a PdoRowInterface.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
#[Column('id', new IntegerType(), false)]
#[Index(['id'], true)]
trait HasIdTrait
{

    protected ?int $id = null;

    /**
     * Gets the public_id of this class.
     *
     * @throws Exception
     */
    final public function getId(): ?int
    {
        return $this->id;
    }

}
