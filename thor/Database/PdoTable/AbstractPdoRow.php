<?php

/**
 * @package Thor/Database/PdoTable
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Database\PdoTable;

use Exception;
use Thor\Database\PdoTable\Attributes\PdoColumn;
use Thor\Database\PdoTable\Attributes\PdoIndex;

#[PdoColumn('public_id', 'VARCHAR(255)', 'string', false)]
#[PdoIndex(['public_id'], true)]
abstract class AbstractPdoRow extends BasePdoRow
{

    use HasPublicId;

    public function __construct(?string $public_id = null, array $primaries = [])
    {
        parent::__construct($primaries);
        $this->public_id = $public_id;
    }

}
