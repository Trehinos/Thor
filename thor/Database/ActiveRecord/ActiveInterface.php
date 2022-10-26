<?php

namespace Thor\Database\ActiveRecord;

use Thor\Database\Criteria;

interface ActiveInterface
{

    public function isSynced(): bool;

    public function reset(): void;

    public function save(): void;

    public static function load(Criteria $criteria): static;

}
