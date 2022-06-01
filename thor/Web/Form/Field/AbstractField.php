<?php

namespace Thor\Web\Form\Field;

use Thor\Web\Node;

/**
 *
 */

/**
 *
 */
abstract class AbstractField extends Node implements FieldInterface
{

    /**
     * @param string      $type
     * @param string      $name
     * @param string|null $value
     * @param bool        $readOnly
     * @param bool        $required
     * @param string|null $htmlClass
     */
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return void
     */
    public function setValue(mixed $value): void
    {
        if (null === $value) {
            $this->value = null;
            return;
        }
        $this->value = (string)$value;
    }

}

