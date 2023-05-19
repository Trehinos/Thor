<?php

namespace Thor\Http\Security\Identity;

/**
 * This interface provides a way to retrieve an Identity from its identifier.
 *
 * @package Thor/Security/Identity
 * @copyright (2021) Sébastien Geldreich
 * @license MIT
 */
interface ProviderInterface
{

    /**
     * Gets an Identity.
     *
     * If no identity is found from the specified identifier, this method MUST return null.
     *
     * @param string $identifier
     *
     * @return IdentityInterface|null
     */
    public function getIdentity(string $identifier): ?IdentityInterface;

}
