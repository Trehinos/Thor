<?php

namespace Thor\Managers;

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
    public function load(array $criteria);

    /**
     * getAll():
     *      Returns the internal array
     *
     * @return array
     */
    public function getAll(): array;

}
