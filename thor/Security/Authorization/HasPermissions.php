<?php

namespace Thor\Security\Authorization;

/**
 *
 */

/**
 *
 */
interface HasPermissions
{

    /**
     * @param string $permission
     *
     * @return bool
     */
    public function hasPermission(string $permission): bool;

}
