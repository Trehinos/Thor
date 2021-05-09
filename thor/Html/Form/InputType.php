<?php

/**
 * @package Trehinos/Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Html\Form;

use Thor\Html\HtmlTag;

class InputType extends HtmlTag implements FieldInterface
{

    private string $value;

    public function __construct(string $type, bool $readOnly = false, bool $required = false, ?string $htmlClass = null)
    {
        parent::__construct('input', true, [
            'type' => $type,
            'readonly' => $readOnly,
            'required' => $required,
            'class' => $htmlClass ?? 'form-control'
        ]);
    }

    public function get(): string
    {
        return $this->value;
    }

    public function set(mixed $value): void
    {
        $this->value = (string) $value;
    }

}
