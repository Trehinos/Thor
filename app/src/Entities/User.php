<?php

namespace App\Entities;

use Thor\Security\BaseDbUser;

class User extends BaseDbUser
{

    public function __construct(string $username = '', string $clearPwd = '')
    {
        parent::__construct($username, $clearPwd);
    }

}
