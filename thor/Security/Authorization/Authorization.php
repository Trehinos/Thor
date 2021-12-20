<?php

namespace Thor\Security\Authorization;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Authorization
{

    private array $permissions;

    public function __construct(string ...$permissions)
    {
        $this->permissions = $permissions;
    }

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
