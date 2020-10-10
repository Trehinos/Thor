<?php

namespace Thor\Html\Form;

use Thor\Html\HtmlTag;

class InputType extends HtmlTag implements FieldInterface
{

    /**
     * @var mixed
     */
    private $value;

    public function __construct(string $name, string $type, bool $required = false, bool $readOnly = false)
    {
        parent::__construct(
            'input',
            true,
            [
                'type' => $type,
                'readonly' => $readOnly,
                'required' => $required,
                'name' => $name,
                'id' => $name,
                'class' => 'form-control'
            ]
        );
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
