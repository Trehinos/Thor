<?php

namespace Thor\Html\Form;

use Thor\Html\HtmlTag;
use Thor\Http\Request;

abstract class Form extends HtmlTag implements FormInterface
{

    private array $data;

    public function __construct(string $action, string $method = Request::POST)
    {
        parent::__construct('form', true, ['action' => $action, 'method' => $method]);
    }

    abstract public static function formDefinition(): array;

    public function setData(array $data): void
    {
        $this->data = $data + $this->data;
    }

    public function getFields(): array
    {
        return $this->data;
    }

    public function getChildren(): array
    {
        return array_values(static::formDefinition());
    }

}
