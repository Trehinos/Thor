<?php

namespace Thor\Http\Client;

use Thor\Http\Uri;
use Thor\Web\Headers;
use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use Thor\Http\Request\HttpMethod;
use Thor\Http\Request\RequestFactory;
use Thor\Http\Request\RequestInterface;
use Thor\Http\Response\ResponseInterface;

/**
 *
 */

/**
 *
 */
abstract class HttpClient implements ClientInterface
{

    /**
     * @param CurlClient $curl
     * @param string     $baseUrl
     */
    public function __construct(
        private CurlClient $curl,
        protected string $baseUrl
    ) {
    }

    /**
     * @param HttpMethod        $method
     * @param string            $operation
     * @param array             $queryParameters
     * @param array|string|null $data
     * @param Headers           $headers
     * @param bool              $isJson
     * @param bool              $isAuthenticated
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function operation(
        HttpMethod $method,
        string $operation,
        array $queryParameters = [],
        array|string|null $data = null,
        Headers $headers = new Headers(),
        bool $isJson = true,
        bool $isAuthenticated = false
    ): ResponseInterface {
        if ($isAuthenticated && ($token = $this->getAuthenticationToken())) {
            $headers->authorization(Headers::AUTHORIZATION_BEARER, ['token' => $token]);
        }

        $uri = Uri::create("{$this->baseUrl}/$operation")->withQuery($queryParameters);
        Logger::write("HTTPClient request : {$method->value} $uri");

        return $this->sendRequest(
            RequestFactory::http11Request(
                $method,
                $uri,
                match (true) {
                    $method === HttpMethod::GET, !$isJson && $data === null => '',
                    $isJson => match (true) {
                        is_array($data) => json_encode($data),
                        $data === null => '{}',
                        default => $data
                    },
                    is_array($data) => http_build_query($data),
                    default => $data
                },
                $headers->get(),
                $isJson ? Headers::TYPE_JSON : Headers::TYPE_TEXT
            )
        );
    }

    /**
     * @return string|null
     */
    abstract public function getAuthenticationToken(): ?string;

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public final function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->curl->sendRequest($request);
    }

}
