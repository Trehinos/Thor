<?php

/**
 * @package          Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Html\Form;

use Thor\Html\HtmlTag;
use Thor\Http\Request\HttpMethod;

abstract class Form extends HtmlTag implements FormInterface
{

    private array $data;

    public function __construct(string $action, HttpMethod $method = HttpMethod::POST)
    {
        parent::__construct('form', true, ['action' => $action, 'method' => $method->value]);
    }

    public function getChildren(): array
    {
        return array_values(static::formDefinition());
    }

    public static function formDefinition(): array
    {
        return [];
    }

    public function setData(array $data): void
    {
        $this->data = $data + $this->data;
    }

    public function getFields(): array
    {
        return $this->data;
    }

}
