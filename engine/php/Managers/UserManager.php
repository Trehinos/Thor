<?php

namespace Thor\Managers;

use Thor\Database\CrudHelper;

final class UserManager
{

    private CrudHelper $userCrud;
    private array $users = [];

    public function __construct(CrudHelper $userCrud)
    {
        $this->userCrud = $userCrud;
    }

}

