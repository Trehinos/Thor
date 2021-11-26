<?php

/**
 * Represent a PdoRow user implementing UserInterface.
 *
 * @package          Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Security\Identity;

use Thor\Security\{PasswordHasher};
use Thor\Database\PdoTable\{PdoRowTrait,
    HasPublicId,
    PdoRowInterface,
    Attributes\PdoRow,
    Attributes\PdoIndex,
    Attributes\PdoColumn
};

#[PdoRow('user', ['id'], 'id')]
#[PdoColumn('id', 'INTEGER', 'integer', false)]
#[PdoColumn('username', 'VARCHAR(255)', 'string', false)]
#[PdoColumn('hash', 'VARCHAR(255)', 'string', false)]
#[PdoIndex(['username'], true)]
class DbUser extends BaseUser implements PdoRowInterface
{

    use PdoRowTrait {
        PdoRowTrait::__construct as private traitConstructor;
    }
    use HasPublicId;

    public function __construct(
        string $username = '',
        string $clearPassword = '',
    ) {
        parent::__construct($username, $clearPassword);
        $this->traitConstructor(['id' => null]);
        $this->public_id = null;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setPwdHashFrom(string $clearPassword): void
    {
        $this->hash = PasswordHasher::hashPassword($clearPassword);
    }
}
