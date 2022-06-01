<?php

namespace Thor\Database\PdoTable;

/**
 * Adds a "public_id" column to a PdoRowInterface.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
interface HasPublicId
{

    /**
     * @return string|null
     */
    public function getPublicId(): ?string;

}
