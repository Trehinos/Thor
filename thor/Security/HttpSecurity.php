<?php

namespace Thor\Security;

use Thor\Security\{Authentication\SessionAuthenticator};
use Thor\Framework\Security\DbUser;
use Thor\Framework\Security\DbUserProvider;
use Thor\Database\{PdoTable\CrudHelper, PdoExtension\PdoRequester};

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

    public function __construct(
        PdoRequester $requester,
        array $firewalls = []
    ) {
        parent::__construct(
            new DbUserProvider(new CrudHelper(DbUser::class, $requester), 'username'),
            new SessionAuthenticator(),
            $firewalls
        );
    }

}
