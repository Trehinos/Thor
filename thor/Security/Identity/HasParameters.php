<?php

namespace Thor\Security\Identity;

interface HasParameters
{
    public function setParameter(string $key, mixed $value): void;

    public function getParameter(string $key): mixed;

    public function getParameters(): array;
}