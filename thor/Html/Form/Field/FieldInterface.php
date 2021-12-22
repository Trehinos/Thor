<?php

namespace Thor\Html\Form\Field;

use Thor\Html\NodeInterface;

/**
 *
 *
 * @package Thor/Html/From
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
interface FieldInterface extends NodeInterface
{

    public function getName(): string;

    public function getValue(): mixed;

    public function setValue(mixed $value): void;

}
