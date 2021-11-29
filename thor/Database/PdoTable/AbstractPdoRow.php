<?php

namespace Thor\Database\PdoTable;

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
abstract class AbstractPdoRow extends BasePdoRow implements HasPublicIdInterface
{

    use HasPublicIdTrait;

    public function __construct(?string $public_id = null, array $primaries = [])
    {
        parent::__construct($primaries);
        $this->public_id = $public_id;
    }

}
