<?php

namespace Thor\Security;

use Thor\Http\Response\ResponseInterface;
use Thor\Http\Request\ServerRequestInterface;
use Thor\Http\Server\RequestHandlerInterface;

/**
 * Default implementation of SecurityInterface::process().
 *
 * @method Security::getAuthenticator()
 * @method getFirewalls()
 *
 * @package          Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
trait ProtectWithFirewalls
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var Firewall $firewall
         */
        foreach ($this->getFirewalls() as $firewall) {
            $firewall->userIsAuthenticated = $this->getAuthenticator()->isAuthenticated();
            if ($firewall->matches($request)) {
                return $firewall->process($request, $handler);
            }
        }
        return $handler->handle($request);
    }

}
