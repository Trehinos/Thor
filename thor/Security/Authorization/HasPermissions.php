<?php

namespace Thor\Security\Authorization;

interface HasPermissions
{

    public function hasPermission(string $permission): bool;

}
