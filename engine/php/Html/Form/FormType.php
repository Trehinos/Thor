<?php

namespace Thor\Html\Form;

use Thor\Html\HtmlInterface;
use Thor\Html\HtmlTag;
use Thor\Http\Request;

class FormType extends HtmlTag implements FormInterface
{

    protected HtmlTag $tag;

    public function __construct(string $action, string $method = Request::POST)
    {
        parent::__construct('form', true, ['action' => $action, 'method' => $method]);
    }

    public function formBuilder(HtmlInterface $html): FormInterface
    {
        // TODO: Implement formBuilder() method.
    }

    public function setData(array $data): void
    {
        // TODO: Implement setData() method.
    }

    public function getFields(): array
    {
        // TODO: Implement getFields() method.
    }

    public function get(string $name): FieldInterface
    {
        // TODO: Implement get() method.
    }

    public function set(string $name, $value): FormInterface
    {
        // TODO: Implement set() method.
    }
}
