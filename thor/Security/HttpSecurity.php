<?php

namespace Thor\Security;

use Thor\Database\{PdoTable\CrudHelper, PdoExtension\PdoRequester};
use Thor\Http\{Response\ResponseInterface, Request\ServerRequestInterface};
use Thor\Security\{Identity\DbUser, Identity\DbUserProvider, Authentication\SessionAuthenticator};

class HttpSecurity extends Security
{
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

    public function protect(ServerRequestInterface $request): ?ResponseInterface
    {
        foreach ($this->getFirewalls() as $firewall) {
            $firewall->isAuthenticated = $this->getAuthenticator()->isAuthenticated();
            if ($firewall->redirect($request)) {
                return $firewall->handle($request);
            }
        }
        return null;
    }
}
