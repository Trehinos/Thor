<?php

/**
 * @package Thor/Validation
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Validation;

interface ValidatorInterface
{

    public function isValid(mixed $value): bool;

}
