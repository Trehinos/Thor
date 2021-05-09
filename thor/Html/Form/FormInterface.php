<?php

/**
 * @package Trehinos/Thor/Html
 * @copyright (2021) Sébastien GELDREICH
 * @license MIT
 */

namespace Thor\Html\Form;

interface FormInterface
{

    public static function formDefinition(): array;

    public function setData(array $data): void;

    public function getFields(): array;

}
