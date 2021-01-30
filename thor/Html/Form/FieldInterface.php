<?php

namespace Thor\Html\Form;

use Thor\Html\HtmlInterface;

interface FieldInterface extends HtmlInterface
{

    public function get(): mixed;

    public function set(mixed $value): void;

}
