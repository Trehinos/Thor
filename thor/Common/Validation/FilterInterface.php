<?php

namespace Thor\Common\Validation;

/**
 * Defines a way to filter data.
 *
 * @package          Thor/Validation
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
interface FilterInterface
{

    /**
     * Returns the value if the data passes the filter or `false` if the filter fails.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function filter(mixed $value): mixed;

}
