<?php

namespace App\Entities;

use Thor\Database\PdoExtension\PdoRow;
use Thor\Security\BaseDbUser;

#[PdoRow('user', ['primary' => ['id']])]
class User extends BaseDbUser
{

    public function __construct(string $username = '', string $clearPwd = '')
    {
        parent::__construct($username, $clearPwd);
    }

}
