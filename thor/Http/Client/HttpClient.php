<?php

namespace Thor\Http\Client;

use Thor\Http\Uri;
use Thor\Debug\Logger;
use Thor\Debug\LogLevel;
use Thor\Framework\Factories\Headers;
use Thor\Http\Request\HttpMethod;
use Thor\Framework\Factories\RequestFactory;
use Thor\Http\Request\RequestInterface;
use Thor\Http\Response\ResponseInterface;

abstract class HttpClient implements ClientInterface
{

    public function __construct(
        private CurlClient $curl,
        protected string $baseUrl
    ) {
    }

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
        Logger::write("HTTPClient request : {$method->value} $uri", LogLevel::NOTICE);

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

    abstract public function getAuthenticationToken(): ?string;

    public final function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->curl->sendRequest($request);
    }

}
