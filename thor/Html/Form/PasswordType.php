<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Html\Form;

class PasswordType extends InputType
{

    public function __construct(bool $readOnly = false, bool $required = false, ?string $htmlClass = null)
    {
        parent::__construct('password', $readOnly, $required, $htmlClass);
        $this->set('');
    }

}
