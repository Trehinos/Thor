<?php

namespace Thor\Http\Security\Authorization;

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
