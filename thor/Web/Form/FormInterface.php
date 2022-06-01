<?php

/**
 * @package Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Web\Form;

use Thor\Web\NodeInterface;
use Thor\Http\Request\HttpMethod;
use Thor\Web\Form\Field\FieldInterface;

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

    /**
     * @param string $name
     *
     * @return FieldInterface|null
     */
    public function getField(string $name): ?FieldInterface;

}
