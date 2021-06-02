<?php

namespace Thor\Database\PdoTable;

use Throwable;
use RuntimeException;
use JetBrains\PhpStorm\Pure;

class PdoRowException extends RuntimeException
{

    #[Pure]
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
