<?php

/**
 * @package          Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Web\Form;

use Thor\Web\Node;
use Thor\Http\Request\HttpMethod;
use Thor\Web\Form\Field\FieldInterface;

/**
 *
 */

/**
 *
 */
abstract class Form extends Node implements FormInterface
{

    /**
     * @param string     $action
     * @param HttpMethod $method
     */
    public function __construct(private string $action, private HttpMethod $method = HttpMethod::POST)
    {
        parent::__construct('form');
        $this->setAttribute('action', $this->action);
        $this->setAttribute('method', $this->method->value);
        $this->setData(static::formDefinition());
    }

    /**
     * @return string
     */
    public function getAction(): string {
        return $this->action;
    }

    /**
     * @return HttpMethod
     */
    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getFieldValues(): array
    {
        $values = [];
        foreach ($this->getFields() as $fieldName => $field) {
            $values[$fieldName] = $field->getValue();
        }
        return $values;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getFieldValue(string $name): mixed
    {
         return $this->getField($name)?->getValue();
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function setFieldsData(array $data): void
    {
        foreach ($data as $fieldName => $value) {
            $this->getField($fieldName)?->setValue($value);
        }
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function setFields(array $data): void
    {
        $this->setData(array_merge($this->getFields(), $data));
    }

    /**
     * @param string $name
     *
     * @return FieldInterface|null
     */
    public function getField(string $name): ?FieldInterface
    {
        return $this->getFields()[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->getChildren();
    }

}
