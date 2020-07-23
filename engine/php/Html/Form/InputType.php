<?php

namespace Thor\Html\Form;

use Thor\Html\HtmlTag;

class InputType extends HtmlTag implements FieldInterface
{

    /**
     * @var mixed
     */
    private $value;

    public function __construct(string $type, bool $readOnly = false, bool $required = false)
    {
        parent::__construct('input', true, ['type' => $type, 'readonly' => $readOnly, 'required' => $required]);
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    public function set($value): void
    {
        $this->value = $value;
    }

}
