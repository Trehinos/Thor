<?php

namespace Thor\Html\Form;

class TextType extends InputType
{

    public function __construct(?string $pattern = null, bool $readOnly = false, bool $required = false)
    {
        parent::__construct('text', $readOnly, $required);
        if (null !== $pattern) {
            $this->setAttr('pattern', $pattern);
        }

        $this->set('');
    }

}
