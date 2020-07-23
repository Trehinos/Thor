<?php

namespace Thor\Html\Form;

use Thor\Html\HtmlInterface;

interface FieldInterface extends HtmlInterface
{

    public function get();

    public function set($value): void;

}
