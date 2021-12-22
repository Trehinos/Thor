<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Html\Form\Field;

class TextField extends InputField
{

    public function __construct(string $name, ?string $pattern = null, bool $read_only = false, bool $required = false, ?string $htmlClass = null)
    {
        parent::__construct($name, 'text', $read_only, $required, $htmlClass);
        if (null !== $pattern) {
            $this->setAttribute('pattern', $pattern);
        }
    }

}
