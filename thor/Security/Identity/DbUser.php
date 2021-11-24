<?php

/**
 * Represent a PdoRow user implementing UserInterface.
 *
 * @package          Trehinos/Thor/Api
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Security\Identity;

use Thor\Database\PdoTable\Attributes\{PdoColumn, PdoIndex, PdoRow};
use Thor\Database\PdoTable\HasPublicId;
use Thor\Database\PdoTable\PdoRowInterface;
use Thor\Database\PdoTable\PdoRowTrait;
use Thor\Security\{PasswordHasher};

#[PdoRow('user', ['id'], 'id')]
#[PdoColumn('id', 'INTEGER', 'integer', false)]
#[PdoColumn('username', 'VARCHAR(255)', 'string', false)]
#[PdoIndex(['username'], true)]
#[PdoColumn('password', 'VARCHAR(255)', 'string', false)]
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
