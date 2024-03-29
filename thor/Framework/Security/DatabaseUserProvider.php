<?php

namespace Thor\Framework\Security;

use Thor\Security\Identity\ProviderInterface;
use Thor\Database\PdoTable\{Criteria, CrudHelper};

/**
 * This class provides a way to retrieve a DbUser from DB.
 *
 * @package          Thor/Security/Identity
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class DatabaseUserProvider implements ProviderInterface
{

    /**
     * @param CrudHelper $userCrud
     * @param string     $usernameField
     */
    public function __construct(
        private CrudHelper $userCrud,
        private string $usernameField
    ) {
    }

    /**
     * @param string $identifier
     *
     * @return DbUser|null
     */
    public function getIdentity(string $identifier): ?DbUser
    {
        return $this->userCrud->readOneBy(new Criteria([$this->usernameField => $identifier]));
    }

}
