<?php

namespace Thor\Http\Security\Identity;

/**
 * This interface provides a way to identify a user.
 *
 * @package Thor/Security/Identity
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
interface IdentityInterface
{

    /**
     * Gets this identity identifier (a unique string identifying this identity).
     *
     * @return string
     */
    public function getIdentifier(): string;

}
