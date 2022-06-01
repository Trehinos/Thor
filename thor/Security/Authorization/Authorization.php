<?php

namespace Thor\Security\Authorization;

use Attribute;

/**
 *
 */

/**
 *
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Authorization
{

    private array $permissions;

    /**
     * @param string ...$permissions
     */
    public function __construct(string ...$permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @param HasPermissions $hasPermissions
     *
     * @return bool
     */
    public function isAuthorized(HasPermissions $hasPermissions): bool
    {
        foreach ($this->permissions as $permission) {
            if (!$hasPermissions->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

}
