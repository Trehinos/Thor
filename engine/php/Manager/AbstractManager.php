<?php

namespace Thor\Manager;

use Thor\Database\CrudHelper;
use Thor\Database\PdoRowInterface;

final class AbstractManager implements ManagerInterface
{

    // todo : ADD FormHelper
    private CrudHelper $crud;
    private array $objects = [];

    public function __construct(CrudHelper $crud)
    {
        $this->crud = $crud;
    }

    public function load(array $criteria = []): void
    {
        if (empty($criteria)) {
            $this->objects = $this->crud->listAll();
            return;
        }
        // todo : load from criteria
    }

    /**
     * @return PdoRowInterface[]
     */
    public function getAll(): array
    {
        return $this->objects;
    }
}

