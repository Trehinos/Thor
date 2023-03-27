<?php

namespace Thor\Ngine\Action;

use Thor\Http\Routing\Route;
use Thor\Http\HttpController;
use Thor\Http\Response\Response;
use Thor\Http\Server\HttpServer;

class HttpAction extends Action {
    public function __construct(
        string $name,
        protected Route $route,
        protected HttpServer $server
    )
    {
        parent::__construct($name);
    }

    public function action(...$args): Response {
        return Response::create('');
    }

    public function url(array $parameters): string {
        return $this->route->url($parameters);
    }

    final public function execute(): void
    {
        $controller = new class ($this, $this->server) extends HttpController {
            public function __construct(private HttpAction $a, HttpServer $httpServer)
            {
                parent::__construct($httpServer);
            }

            public function action(...$args): Response {
                return $this->a->action(...$args);
            }
        };

        $controller->action(...$this->route->getFilledParams());
    }

}
