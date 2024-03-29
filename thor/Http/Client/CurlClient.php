<?php

namespace Thor\Http\Client;

use CurlHandle;
use Thor\Http\Response\ResponseFactory;
use Thor\Http\{Response\Response,
    Request\HttpMethod,
    Response\HttpStatus,
    Request\RequestInterface,
    Response\ResponseInterface
};

/**
 * Provides an implementation of ClientInterface to send requests with Curl.
 *
 * @see              MessageInterface
 *
 * @package          Thor/Http/Client
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
final class CurlClient implements ClientInterface
{

    private ?CurlHandle $curl;
    private ?RequestInterface $preparedRequest = null;
    private array $responseHeaders = [];
    private array $curlOptions = [];

    private array $authInfo = [];

    public function __construct(array $curlOptions = [], ?string $basicUser = null, ?string $basicPwd = null)
    {
        $this->curl = curl_init();
        $this->curlOptions = $curlOptions;
        if ($basicUser !== null && $basicPwd !== null) {
            $this->authInfo = [
                'user'     => $basicUser,
                'password' => $basicPwd,
            ];
        }
    }

    /**
     * Sends the specified request and returns the server's response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this
                   ->prepare($request)
                   ->execute() ?? ResponseFactory::notFound();
    }

    /**
     * Sends the request to the server.
     *
     * @return ResponseInterface|null
     */
    public function execute(): ?ResponseInterface
    {
        if ($this->preparedRequest === null) {
            return null;
        }
        $body = curl_exec($this->curl);
        $status = curl_getinfo($this->curl, CURLINFO_RESPONSE_CODE);
        $this->preparedRequest = null;

        return Response::create($body, HttpStatus::from($status), $this->responseHeaders);
    }

    /**
     * Prepares a request to be sent with `execute()`.
     *
     * @param RequestInterface $request
     *
     * @return $this
     */
    public function prepare(RequestInterface $request): self
    {
        $this->preparedRequest = $request;
        $this->responseHeaders = [];
        curl_setopt_array(
            $this->curl,
            $this->curlOptions + [
                CURLOPT_USERAGENT      => 'Thor/CurlClient',
                CURLOPT_URL            => $request->getRequestTarget(),
                CURLOPT_HTTPHEADER     => self::toHeadersLines($request->getHeaders()),
                CURLOPT_HTTPGET        => $request->getMethod() === HttpMethod::GET,
                CURLOPT_POST           => $request->getMethod() === HttpMethod::POST,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADERFUNCTION => function (CurlHandle $curlHandle, string $headerString) {
                    $header = explode(':', $headerString, 2);
                    if (count($header) === 2) {
                        $this->responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);
                    }
                    return strlen($headerString);
                },
            ]
        );
        if (!empty($this->authInfo)) {
            curl_setopt($this->curl, CURLOPT_USERPWD, "{$this->authInfo['user']}:{$this->authInfo['password']}");
        }
        if ($this->preparedRequest->getMethod() !== HttpMethod::GET) {
            curl_setopt(
                $this->curl,
                CURLOPT_POSTFIELDS,
                str_ends_with($request->getHeaders()['Content-Type'] ?? '', 'json')
                    ? $this->preparedRequest->getBody()->getContents()
                    : json_decode($this->preparedRequest->getBody()->getContents(), true)
            );
        }

        if (!in_array($request->getMethod(), [HttpMethod::GET, HttpMethod::POST])) {
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $request->getMethod()->value);
        }

        return $this;
    }

    /**
     * Transforms a ['Header' => value] array to an array :
     *  * ['Header: value'] if value is a string,
     *  * ['Header: value1, value2...'] if value is an array.
     *
     * @param array $headers
     *
     * @return array
     */
    public static function toHeadersLines(array $headers): array
    {
        return array_map(
            fn(string $key, array|string $value) => "$key: " . (is_string($value) ? $value : implode(', ', $value)),
            array_keys($headers),
            array_values($headers),
        );
    }

    /**
     * Returns the CurlHandle of this client.
     *
     * @return CurlHandle
     */
    public function getCurlHandle(): CurlHandle
    {
        return $this->curl;
    }

    /**
     *
     */
    public function __destruct()
    {
        if ($this->curl instanceof CurlHandle) {
            curl_close($this->curl);
        }
    }

}
