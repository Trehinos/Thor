<?php

namespace Thor\Validation;

interface FilterInterface
{

    public function filter(mixed $value): mixed;

}
