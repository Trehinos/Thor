<?php

namespace Thor\Database\PdoTable;

use Exception;
use Thor\Database\PdoTable\Attributes\PdoIndex;
use Thor\Database\PdoTable\Attributes\PdoColumn;

#[PdoColumn('public_id', 'VARCHAR(255)', 'string', false)]
#[PdoIndex(['public_id'], true)]
trait HasPublicId
{

    protected ?string $public_id = null;

    /**
     * @throws Exception
     */
    final public function getPublicId(): ?string
    {
        return $this->public_id;
    }

    /**
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
