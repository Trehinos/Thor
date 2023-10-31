<?php

namespace Thor\Framework\Security;

use Thor\Security\PasswordHasher;
use Thor\Security\Identity\BaseUser;
use Thor\Database\PdoTable\HasPublicId;
use Thor\Database\PdoTable\HasPublicIdTrait;
use Thor\Database\PdoTable\PdoRow\PdoRowTrait;
use Thor\Database\PdoTable\PdoRow\Attributes\{Column};
use Thor\Database\PdoTable\PdoRow\RowInterface;
use Thor\Database\PdoTable\PdoRow\Attributes\Index;
use Thor\Database\PdoTable\PdoRow\Attributes\Table;
use Thor\Database\PdoTable\TableType\{ArrayType, StringType, IntegerType};

/**
 * This extension of BaseUser gives a way to have an Identity stored in DB.
 *
 * @package Thor/Security/Identity
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
#[Table('user', ['id'], 'id')]
#[Column('id', new IntegerType(), false)]
#[Column('username', new StringType(), false)]
#[Column('hash', new StringType(), false)]
#[Column('permissions', new ArrayType(4096), false)]
#[Column('parameters', new ArrayType(4096), false)]
#[Index(['username'], true)]
class DbUser extends BaseUser implements RowInterface, HasPublicId
{

    
    use PdoRowTrait {
        PdoRowTrait::__construct as private traitConstructor;
    }
    use HasPublicIdTrait;

    /**
     * @param int|null    $id
     * @param string      $username
     * @param string      $clearPassword
     * @param array       $permissions
     * @param array       $parameters
     * @param string|null $public_id
     */
    public function __construct(
        ?int $id = null,
        string $username = '',
        string $clearPassword = '',
        array $permissions = [],
        array $parameters = [],
        ?string $public_id = null
    ) {
        parent::__construct($username, $clearPassword, $permissions, $parameters);
        $this->traitConstructor(['id' => $id]);
        $this->public_id = $public_id;
    }

    /**
     * @param string $username
     *
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @param string $clearPassword
     *
     * @return void
     */
    public function setPwdHashFrom(string $clearPassword): void
    {
        $this->hash = PasswordHasher::hashPassword($clearPassword);
    }

}
