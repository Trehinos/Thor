<?php

namespace Thor\Html\Form;

use Thor\Html\HtmlInterface;

interface FormInterface
{

    public function formBuilder(HtmlInterface $html): self;

    public function setData(array $data): void;

    public function getFields(): array;

    public function get(string $name): FieldInterface;

    public function set(string $name, $value): self;



}
