<?php

namespace Thor\Database\PdoTable;

use Exception;
use Thor\Database\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\Attributes\PdoColumn;

/**
 * Adds a "public_id" column to a PdoRowInterface.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
#[PdoColumn('public_id', 'VARCHAR(255)', 'string', false)]
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
     * Generates a new public_id in this hexadecimal form :
     *
     * xxxx-xxxx-xxxx-xxxx-xxxxxxxx-xxxxxxxx
     *
     * @throws Exception
     */
    public function generatePublicId(): void
    {
        $this->public_id = bin2hex(random_bytes(2)) .
                           '-' . bin2hex(random_bytes(2)) .
                           '-' . bin2hex(random_bytes(2)) .
                           '-' . bin2hex(random_bytes(2)) .
                           '-' . bin2hex(random_bytes(4)) .
                           '-' . bin2hex(random_bytes(4));
    }

}
