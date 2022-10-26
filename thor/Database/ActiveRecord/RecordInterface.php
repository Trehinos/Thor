<?php

namespace Thor\Database\ActiveRecord;

interface RecordInterface
{

    public function pk(): array;

    public function index(string $index): array;

    public function fk(array $keyValue): mixed;
    public function  __set(string $name, mixed $value): void;
    public function  __get(string $name): mixed;

}
