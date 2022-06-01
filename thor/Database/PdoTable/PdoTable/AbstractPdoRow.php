<?php

namespace Thor\Database\PdoTable\PdoTable;

use Thor\Database\PdoTable\HasPublicId;
use Thor\Database\PdoTable\HasPublicIdTrait;

/**
 * Merges BasePdoRow and HasPublicId.
 *
 * @see BasePdoRow
 * @see HasPublicIdTrait
 *
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
abstract class AbstractPdoRow extends BasePdoRow implements HasPublicId
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
