<?php

namespace Thor\Debug;

use Exception;
use Throwable;
use Thor\Tools\Strings;

class ThorException extends Exception
{

    public function __construct(int $code, string $details, array $context = [], ?Throwable $previous = null)
    {
        parent::__construct(Strings::interpolate($details, [...$context, ...['code' => $code]]), $code, $previous);
    }

}
