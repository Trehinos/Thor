<?php

/**
 * @package Trehinos/Thor/Validation
 * @copyright (2021) Sébastien GELDREICH
 * @license MIT
 */

namespace Thor\Validation;

interface ValidatorInterface
{

    public function isValid(mixed $value): bool;

}
