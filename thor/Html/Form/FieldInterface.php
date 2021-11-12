<?php

/**
 * @package Trehinos/Thor/Html
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */

namespace Thor\Html\Form;

use Thor\Html\HtmlInterface;

interface FieldInterface extends HtmlInterface
{

    public function get(): mixed;

    public function set(mixed $value): void;

}
