<?php

namespace Thor\Validation;

/**
 * Defines a way to validate data.
 *
 * @package          Thor/Validation
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
interface ValidatorInterface
{

    /**
     * Returns true if the value is valid for this Validator.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isValid(mixed $value): bool;

}
