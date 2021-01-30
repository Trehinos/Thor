<?php

namespace App\Entities;

use Thor\Database\DefinitionHelper;
use Thor\Security\BaseDbUser;
use Thor\Thor;

class User extends BaseDbUser
{

    public function __construct(string $username = '', string $clearPwd = '')
    {
        parent::__construct($username, $clearPwd);
    }

    public static function getTableName(): string
    {
        return 'user';
    }

    public static function getDefinitionHelper(): ?DefinitionHelper
    {
        return new DefinitionHelper(Thor::getInstance()->getDefinitionHelperConfiguration());
    }
}
