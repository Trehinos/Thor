<?php

/**
 * @package Trehinos/Thor/Validation
 * @copyright (2021) Sébastien GELDREICH
 * @license MIT
 */

namespace Thor\Validation;

interface FilterInterface
{

    public function filter(mixed $value): mixed;

}
