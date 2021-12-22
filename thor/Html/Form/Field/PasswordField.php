<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Html\Form\Field;

class PasswordField extends InputField
{

    public function __construct(string $name, bool $readOnly = false, bool $required = false, ?string $htmlClass = null)
    {
        parent::__construct($name, 'password', $readOnly, $required, $htmlClass);
    }

}
