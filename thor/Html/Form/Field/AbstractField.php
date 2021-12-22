<?php

namespace Thor\Html\Form\Field;

use Thor\Html\Node;

abstract class AbstractField extends Node implements FieldInterface
{

    public function __construct(
        string $type,
        protected string $name,
        protected ?string $value = null,
        bool $readOnly = false,
        bool $required = false,
        ?string $htmlClass = null
    ) {
        parent::__construct($type);
        $this->setAttribute('name', $this->name);
        $this->setAttribute('value', $this->value);
        $this->setAttribute('required', $required);
        $this->setAttribute('readonly', $readOnly);
        $this->setAttribute('class', $htmlClass ?? 'form-control');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        if (null === $value) {
            $this->value = null;
            return;
        }
        $this->value = (string)$value;
    }

}

