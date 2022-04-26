<?php

namespace Thor\Database\PdoTable;

/**
 * Adds an "id" column to a PdoRowInterface.
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
interface HasId
{

    public function getId(): ?int;

}
