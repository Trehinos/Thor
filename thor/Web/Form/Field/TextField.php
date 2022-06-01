<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Web\Form\Field;

/**
 *
 */

/**
 *
 */
class TextField extends InputField
{

    /**
     * @param string      $name
     * @param string|null $pattern
     * @param bool        $read_only
     * @param bool        $required
     * @param string|null $htmlClass
     */
    public function __construct(string $name, ?string $pattern = null, bool $read_only = false, bool $required = false, ?string $htmlClass = null)
    {
        parent::__construct($name, 'text', $read_only, $required, $htmlClass);
        if (null !== $pattern) {
            $this->setAttribute('pattern', $pattern);
        }
    }

}
