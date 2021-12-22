<?php

/**
 * @package          Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Html\Form;

use Thor\Html\Node;
use Thor\Http\Request\HttpMethod;
use Thor\Html\Form\Field\FieldInterface;

abstract class Form extends Node implements FormInterface
{

    public function __construct(private string $action, private HttpMethod $method = HttpMethod::POST)
    {
        parent::__construct('form');
        $this->setAttribute('action', $this->action);
        $this->setAttribute('method', $this->method->value);
        $this->setData(static::formDefinition());
    }

    public function getAction(): string {
        return $this->action;
    }

    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    public function getFieldValues(): array
    {
        $values = [];
        foreach ($this->getFields() as $fieldName => $field) {
            $values[$fieldName] = $field->getValue();
        }
        return $values;
    }

    public function getFieldValue(string $name): mixed
    {
         return $this->getField($name)?->getValue();
    }

    public function setFieldsData(array $data): void
    {
        foreach ($data as $fieldName => $value) {
            $this->getField($fieldName)?->setValue($value);
        }
    }

    public function setFields(array $data): void
    {
        $this->setData(array_merge($this->getFields(), $data));
    }

    public function getField(string $name): ?FieldInterface
    {
        return $this->getFields()[$name] ?? null;
    }

    public function getFields(): array
    {
        return $this->getChildren();
    }

}
