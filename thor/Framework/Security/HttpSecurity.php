<?php

namespace Thor\Framework\Security;

use Thor\Database\{PdoTable\CrudHelper, PdoExtension\Requester};
use Thor\Security\{Security, ProtectWithFirewalls, Authentication\SessionAuthenticator};

/**
 * Default HTTP security context.
 *
 * @package          Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class HttpSecurity extends Security
{

    use ProtectWithFirewalls;

    /**
     * @param Requester $requester
     * @param array        $firewalls
     * @param class-string $className
     * @param string       $usernameField
     */
    public function __construct(
        Requester $requester,
        array $firewalls = [],
        string $className = DbUser::class,
        string $usernameField = 'username'
    ) {
        parent::__construct(
            new DatabaseUserProvider(new CrudHelper($className, $requester), $usernameField),
            new SessionAuthenticator(),
            $firewalls
        );
    }

}
