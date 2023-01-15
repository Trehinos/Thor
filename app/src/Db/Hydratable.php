<?php

namespace Evolution\Db;

interface Hydratable
{

    public static function hydrates(array $pdoArray): static;

    public function toPdoArray(): array;

}
