<?php

namespace Thor\Database\PdoTable\PdoRow;

use Thor\Database\PdoTable\HasPublicId;
use Thor\Database\PdoTable\HasPublicIdTrait;
use Thor\Database\PdoTable\PdoRow\Row;

/**
 * Merges BasePdoRow and HasPublicId.
 *
 * @see Row
 * @see HasPublicIdTrait
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
abstract class AbstractRow extends Row implements HasPublicId
{

    use HasPublicIdTrait;

    /**
     * @param string|null $public_id
     * @param array       $primaries
     */
    public function __construct(?string $public_id = null, array $primaries = [])
    {
        parent::__construct($primaries);
        $this->public_id = $public_id;
    }

}
