<?php

namespace Thor\Validation;

interface ValidatorInterface
{

    public function isValid(mixed $value): bool;

}
