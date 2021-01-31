<?php

namespace Thor\Html\Form;

class PasswordType extends InputType
{

    public function __construct(bool $readOnly = false, bool $required = false, ?string $htmlClass = null)
    {
        parent::__construct('password', $readOnly, $required, $htmlClass);
        $this->set('');
    }

}
