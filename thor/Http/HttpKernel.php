<?php

namespace Thor\Http;

use Thor\Env;
use Thor\Thor;
use Thor\KernelInterface;
use Thor\Debug\{Logger, LogLevel};
use Thor\Configuration\Configuration;
use Thor\Framework\Factories\Configurations;
use Thor\Framework\Factories\{HttpServerFactory, ServerRequestFactory};
use Thor\Http\{Server\HttpServer, Response\ResponseInterface, Request\ServerRequestInterface};

/**
 * HttpKernel of Thor. It is by default instantiated with the `api.php` entry point.
 *
 * @package          Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class HttpKernel implements KernelInterface
{

    /**
     * @param HttpServer $server
     */
    public function __construct(protected HttpServer $server)
    {
        Logger::write('Instantiate HttpKernel');
    }

    /**
     * This function return a new kernel.
     *
     * It loads the configuration files and use it to instantiate the Kernel.
     *
     * @see Configurations::getHttpConfiguration()
     * @see Thor::getConfigurationPool()
     */
    public static function create(): static
    {
        self::guardHttp();
        Logger::write('Start HTTP context');

        return static::createFromConfiguration(Configurations::getHttpConfiguration());
    }

    /**
     * This function exit the program if PHP is run from Cli context.
     *
     * @return void
     */
    final public static function guardHttp(): void
    {
        if ('cli' === php_sapi_name()) {
            Logger::write(
                "PANIC ABORT : HTTP kernel tried to be executed from CLI context.",
                LogLevel::ALERT,
            );
            exit;
        }
    }

    /**
     * This static function returns a new HttpKernel with specified configuration.
     *
     * @param Configuration $config
     *
     * @return static
     * @throws \ReflectionException
     * @throws \ReflectionException
     */
    public static function createFromConfiguration(Configuration $config): static
    {
        return new static(HttpServerFactory::createHttpServerFromConfiguration($config));
    }

    /**
     * This method executes the HttpKernel :
     * 1. Load a ServerRequestInterface from the globals,
     * 2. Let the HttpServer handle it (security, controller, etc),
     * 3. Uses the returned ResponseInterface to send the response.
     *
     * @see ServerRequestInterface
     * @see HttpServer
     * @see HttpController
     * @see ResponseInterface
     */
    final public function execute(): void
    {
        ob_start();
        Logger::write('Server handle the HTTP request');
        $request = $this->alterRequest(ServerRequestFactory::createFromGlobals());
        $response = $this->alterResponse($this->handle($request));
        $responseStatus = $response->getStatus()->normalized();
        $responseCode = $response->getStatus()->value;
        Logger::write("HTTP Response generated ($responseCode : $responseStatus).", LogLevel::DEBUG);

        if (Thor::getEnv() === Env::PROD) {
            ob_clean(); // Prevent accidental echoes or var_dump from controller in prod
        }

        http_response_code($responseCode);                                      // Emit status code

        if (!empty($headers = $response->getHeaders())) {
            Logger::write("Send headers");
            foreach ($headers as $headerName => $headerValue) {                 // Emit headers
                if (is_array($headerValue)) {
                    foreach ($headerValue as $subValue) {
                        header("$headerName: $subValue", false);
                    }
                    continue;
                }
                header("$headerName: $headerValue");
            }
        }

        if ($request->getMethod()->responseHasBody() && ($body = $response->getBody()->getContents()) !== '') {
            Logger::write("Send body");
            echo $body;                                                         // Print body
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function alterResponse(ResponseInterface $response): ResponseInterface
    {
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ServerRequestInterface
     */
    protected function alterRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request;
    }

    /**
     * Makes the HttpServer handle the ServerRequestInterface and returns its ResponseInterface.
     */
    public function handle(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return $this->server->handle($serverRequest);
    }

}
