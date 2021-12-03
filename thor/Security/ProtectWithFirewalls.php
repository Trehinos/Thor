<?php

namespace Thor\Security;

use Thor\Http\Response\ResponseInterface;
use Thor\Http\Request\ServerRequestInterface;

/**
 * Default implementation of SecurityInterface::protect().
 *
 * @method getAuthenticator()
 * @method getFirewalls()
 *
 * @package          Thor/Security
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
trait ProtectWithFirewalls
{

    /**
     * @see SecurityInterface::protect()
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface|null
     */
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
