<?php

namespace Thor\Html\Form;

class PasswordType extends InputType
{

    public function __construct(string $name, bool $required = false, bool $readOnly = false)
    {
        parent::__construct($name,'password', $readOnly, $required);
        $this->set('');
    }

}
