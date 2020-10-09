<?php

namespace Thor\Html\Form;

class PasswordType extends InputType
{

    public function __construct(bool $readOnly = false, bool $required = false)
    {
        parent::__construct('password', $readOnly, $required);
        $this->set('');
    }

}
