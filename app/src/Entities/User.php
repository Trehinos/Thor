<?php

namespace App\Entities;

use Thor\Database\DefinitionHelper;
use Thor\Database\PdoExtension\PdoRow;
use Thor\Security\BaseDbUser;
use Thor\Thor;

#[PdoRow('user')]
class User extends BaseDbUser
{

    public function __construct(string $username = '', string $clearPwd = '')
    {
        parent::__construct($username, $clearPwd);
    }

    public static function getDefinitionHelper(): ?DefinitionHelper
    {
        return new DefinitionHelper(Thor::getInstance()->getDefinitionHelperConfiguration());
    }
}
