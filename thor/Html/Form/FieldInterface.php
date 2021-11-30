<?php

namespace Thor\Html\Form;

use Thor\Html\HtmlInterface;

/**
 *
 *
 * @package Thor/Html/From
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
interface FieldInterface extends HtmlInterface
{

    public function get(): mixed;

    public function set(mixed $value): void;

}
