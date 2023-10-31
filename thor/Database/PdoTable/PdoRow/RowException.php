<?php

namespace Thor\Database\PdoTable\PdoRow;

use Throwable;
use RuntimeException;
use JetBrains\PhpStorm\Pure;

/**
 * This class describes a RuntimeException thrown by the Thor's PdoTable module.
 *
 *
 * @package   Thor\Database\PdoExtension
 *
 * @since     2020-10
 * @version   1.0
 * @author    Trehinos
 * @license   MIT
 */
class RowException extends RuntimeException
{

    /**
     * @param                $message
     * @param                $code
     * @param Throwable|null $previous
     */
    /**
     * @param                $message
     * @param                $code
     * @param Throwable|null $previous
     */
    #[Pure]
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
