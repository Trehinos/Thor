<?php

namespace Thor\Database\ActiveRecord;

use Thor\Database\Criteria;

interface SetInterface
{

    public function add(RecordInterface $record): void;

    public function get(Criteria $criteria): array;

    public function set(RecordInterface $record): void;

    public function remove(RecordInterface $record): void;

}
