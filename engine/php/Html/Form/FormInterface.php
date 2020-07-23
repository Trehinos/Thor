<?php

namespace Thor\Html\Form;

interface FormInterface
{

    public static function formDefinition(): array;

    public function setData(array $data): void;

    public function getFields(): array;

}
