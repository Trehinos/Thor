<?php

namespace Thor\Security;

use Thor\Database\{PdoTable\CrudHelper, PdoExtension\PdoRequester};
use Thor\Security\{Identity\DbUser, Identity\DbUserProvider, Authentication\SessionAuthenticator};

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
