<?php

namespace Thor\Validation;

interface ValidatorInterface
{

    public function isValid($value): bool;

}
