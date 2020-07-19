<?php

namespace Thor\Manager;

use Thor\Database\PdoRowInterface;

interface ManagerInterface
{

    /**
     * load():
     *      Load users from DB with provided criteria and stores results in an internal array
     *
     * ['or' => ['field' = ..., 'field' = ...], 'and' => [...], 'in:field' => [values]]
     *
     * @param array $criteria
     *
     * @return void
     */
    public function load(array $criteria = []): void;

    /**
     * getAll():
     *      Returns the internal array
     *
     * @return PdoRowInterface[]
     */
    public function getAll(): array;

}
