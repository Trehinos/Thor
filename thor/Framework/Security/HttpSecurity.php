<?php

namespace Thor\Framework\Security;

use Thor\Http\Security\Authentication\SessionAuthenticator;
use Thor\Database\{PdoTable\CrudHelper, PdoExtension\PdoRequester};
use Thor\Security\{Thor\Http\Security\Security, Thor\Http\Security\ProtectWithFirewalls};

/**
 * Default HTTP security context.
 *
 * @package          Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class HttpSecurity extends \Thor\Http\Security\Security
{

    use \Thor\Http\Security\ProtectWithFirewalls;

    /**
     * @param PdoRequester $requester
     * @param array        $firewalls
     * @param class-string $className
     * @param string       $usernameField
     */
    public function __construct(
        PdoRequester $requester,
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
