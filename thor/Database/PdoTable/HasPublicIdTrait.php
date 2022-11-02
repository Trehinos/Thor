<?php

namespace Thor\Database\PdoTable;

use Exception;
use Thor\Tools\Guid;
use Thor\Database\Definition\TableType\StringType;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\PdoTable\Attributes\PdoColumn;

/**
 * Adds a "public_id" column to a PdoRowInterface.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
#[PdoColumn('public_id', new StringType(), false)]
#[PdoIndex(['public_id'], true)]
trait HasPublicIdTrait
{

    protected ?string $public_id = null;

    /**
     * Gets the public_id of this class.
     *
     * @throws Exception
     */
    final public function getPublicId(): ?string
    {
        return $this->public_id;
    }

    /**
     * Generates a new GUID formatted public_id..
     *
     * @throws Exception
     */
    public function generatePublicId(): void
    {
        $this->public_id = Guid::hex();
    }

}
