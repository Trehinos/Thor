<?php

namespace Thor\Web;

use JsonException;
use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use Thor\Http\Routing\Route;
use Thor\Http\Response\Response;
use Thor\Http\Response\HttpStatus;
use Thor\Http\Response\ResponseFactory;
use Thor\Http\Response\ResponseInterface;
use Thor\Http\Request\ServerRequestInterface;

class MultiAjax extends WebController
{

    public function __construct(WebServer $webServer)
    {
        parent::__construct($webServer);
    }

    protected function response(mixed $data = null, HttpStatus $status = HttpStatus::NO_CONTENT): Response
    {
        return ResponseFactory::json(
            $data,
            $status === HttpStatus::NO_CONTENT ? ($data !== null ? HttpStatus::OK : $status) : $status
        );
    }

    /**
     * @param HttpStatus $defaultStatus
     * @param mixed      ...$data
     *
     * @return Response
     *
     * @throws JsonException
     */
    protected function multiResponse(HttpStatus $defaultStatus, mixed ...$data): Response
    {
        return ResponseFactory::json([
            array_map(
                fn(mixed $dataChunk) => ResponseFactory::json(
                    $dataChunk,
                    $dataChunk === null
                        ? ($defaultStatus === HttpStatus::OK ? HttpStatus::NO_CONTENT : $defaultStatus)
                        : ($defaultStatus === HttpStatus::NO_CONTENT ? HttpStatus::OK : $defaultStatus)
                )->getRaw(),
                $data
            ),
        ]);
    }

    /**
     * Instantiate the controller class from the Route given in the constructor and returns its response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws JsonException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $responses = [];
        $routes = $request->getAttributes()['routes'] ?? [];
        foreach ($routes as $route) {
            $cClass = $route->getControllerClass();
            $cMethod = $route->getControllerMethod();
            Logger::write(' -> INSTANTIATE {controller} EXECUTE {method}', LogLevel::DEBUG, [
                'controller' => $cClass,
                'method'     => $cMethod,
            ]);
            $controller = new $cClass($this->httpServer);
            $responses[] = $controller->$cMethod(...array_values($this->route->getFilledParams()));
        }

        return $this->multiResponse(HttpStatus::OK, $responses);
    }

}
