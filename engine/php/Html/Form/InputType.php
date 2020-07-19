<?php

namespace Thor\Html\Form;

use Thor\Html\HtmlTag;

class InputType extends HtmlTag implements FieldInterface
{

    public function __construct(string $type, bool $readOnly = false, bool $required = false)
    {
        parent::__construct('input', true, ['readonly' => $readOnly, 'required' => $required]);
    }


    public function get()
    {
        // TODO: Implement get() method.
    }

    public function set($value): void
    {
        // TODO: Implement set() method.
    }
}
