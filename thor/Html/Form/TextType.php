<?php

namespace Thor\Html\Form;

class TextType extends InputType
{

    public function __construct(string $name, ?string $pattern = null, bool $required = false, bool $readOnly = false)
    {
        parent::__construct($name, 'text', $readOnly, $required);
        if (null !== $pattern) {
            $this->setAttr('pattern', $pattern);
        }

        $this->set('');
    }

}
