<?php

namespace Thor\Web\Form\Field;

use Thor\Web\NodeInterface;

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
