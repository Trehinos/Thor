<?php

namespace Thor\Security\Identity;

use Thor\Database\PdoTable\Criteria;
use Thor\Database\PdoTable\CrudHelper;

class DbUserProvider implements ProviderInterface
{

    public function __construct(
        private CrudHelper $userCrud,
        private string $usernameField
    ) {
    }

    public function getIdentity(string $identifier): ?DbUser
    {
        return $this->userCrud->readOneBy(new Criteria([$this->usernameField => $identifier]));
    }

}
