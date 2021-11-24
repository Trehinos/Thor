<?php

namespace Thor\Security;

use Thor\Database\PdoExtension\PdoRequester;
use Thor\Database\PdoTable\CrudHelper;
use Thor\Http\Request\ServerRequestInterface;
use Thor\Http\Response\ResponseInterface;
use Thor\Http\Routing\Router;
use Thor\Security\Authentication\SessionAuthenticator;
use Thor\Security\Identity\DbUser;
use Thor\Security\Identity\DbUserProvider;

class HttpSecurity extends Security
{

    public function __construct(
        private Router $router,
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
            if ($firewall->redirect($request, $this->router)) {
                $request->getAttribute("firewall{$firewall->pattern}", 'FIREWALLED');
                return $firewall->handle($request);
            }
        }

        return null;
    }
}
