<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Html\Form;

use Thor\Html\NodeInterface;
use Thor\Http\Request\HttpMethod;
use Thor\Html\Form\Field\FieldInterface;

interface FormInterface extends NodeInterface
{

    public static function formDefinition(): array;

    public function getAction(): string;

    public function getMethod(): HttpMethod;

    /**
     * @param FieldInterface[] $data
     */
    public function setFields(array $data): void;

    /**
     * @param array $data ['fieldName' => value]
     */
    public function setData(mixed $data): void;

    /**
     * @return FieldInterface[]
     */
    public function getFields(): array;

    public function getField(string $name): ?FieldInterface;

}
