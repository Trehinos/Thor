<?php

namespace Thor\Http\Web\Form\Field;

use Thor\Http\Web\NodeInterface;

/**
 *
 *
 * @package Thor/Html/From
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
interface FieldInterface extends NodeInterface
{

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return mixed
     */
    public function getValue(): mixed;

    /**
     * @param mixed $value
     *
     * @return void
     */
    public function setValue(mixed $value): void;

}
