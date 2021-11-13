<?php

/**
 * @package          Trehinos/Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */

namespace Thor\Http;

use Thor\Env;
use Thor\Thor;
use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use Thor\KernelInterface;
use Thor\Http\Server\HttpServer;
use Thor\Http\Request\ServerRequest;
use Thor\Factories\HttpServerFactory;
use Thor\Factories\ServerRequestFactory;

class HttpKernel implements KernelInterface
{

    public function __construct(protected HttpServer $server)
    {
        Logger::write('Instantiate HttpKernel');
    }

    public static function create(): static
    {
        self::guardHttp();
        Logger::write('Start HTTP context');

        return self::createFromConfiguration(Thor::getConfiguration()->getHttpConfiguration());
    }

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

    public static function createFromConfiguration(array $config = []): static
    {
        return new self(HttpServerFactory::createHttpServerFromConfiguration($config)->produce());
    }

    public function execute(): void
    {
        ob_start();
        Logger::write('Server handle the HTTP request');
        $request = ServerRequestFactory::createFromGlobals();
        $response = $this->server->handle($request);
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

}
