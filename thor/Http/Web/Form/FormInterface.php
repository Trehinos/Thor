<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Http\Web\Form;

use Thor\Http\Web\NodeInterface;
use Thor\Http\Request\HttpMethod;
use Thor\Http\Web\Form\Field\FieldInterface;

/**
 *
 */

/**
 *
 */
interface FormInterface extends NodeInterface
{

    /**
     * @return array
     */
    public static function formDefinition(): array;

    /**
     * @return string
     */
    public function getAction(): string;

    /**
     * @return HttpMethod
     */
    public function getMethod(): HttpMethod;

    /**
     * @param \Thor\Http\Web\Form\Field\FieldInterface[] $data
     */
    public function setFields(array $data): void;

    /**
     * @param array $data ['fieldName' => value]
     */
    public function setData(mixed $data): void;

    /**
     * @return \Thor\Http\Web\Form\Field\FieldInterface[]
     */
    public function getFields(): array;

    /**
     * @param string $name
     *
     * @return \Thor\Http\Web\Form\Field\FieldInterface|null
     */
    public function getField(string $name): ?FieldInterface;

}
