<?php

namespace Thor\Security\Identity;

use Thor\Security\PasswordHasher;
use Thor\Database\PdoTable\PdoRowTrait;
use Thor\Database\PdoTable\PdoRowInterface;
use Thor\Database\PdoTable\HasPublicIdTrait;
use Thor\Database\PdoTable\TableType\{ArrayType, StringType, IntegerType};
use Thor\Database\PdoTable\Attributes\{PdoTable, PdoIndex, PdoColumn};

/**
 * This extension of BaseUser gives a way to have an Identity stored in DB.
 *
 * @package Thor/Security/Identity
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
#[PdoTable('user', ['id'], 'id')]
#[PdoColumn('id', new IntegerType(), false)]
#[PdoColumn('username', new StringType(), false)]
#[PdoColumn('hash', new StringType(), false)]
#[PdoColumn('permissions', new ArrayType(4096), false)]
#[PdoColumn('parameters', new ArrayType(4096), false)]
#[PdoIndex(['username'], true)]
class DbUser extends BaseUser implements PdoRowInterface
{

    use PdoRowTrait {
        PdoRowTrait::__construct as private traitConstructor;
    }
    use HasPublicIdTrait;

    public function __construct(
        string $username = '',
        string $clearPassword = '',
        array $permissions = [],
        array $parameters = [],
        ?string $public_id = null
    ) {
        parent::__construct($username, $clearPassword, $permissions, $parameters);
        $this->traitConstructor(['id' => null]);
        $this->public_id = $public_id;
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
