<?php

namespace Thor\Factories;

use Thor\Thor;
use Thor\Http\UriInterface;
use Thor\Http\Request\Request;
use Thor\Http\Request\HttpMethod;

class RequestFactory
{

    public static string $userAgent = 'Thor-' . Thor::VERSION . '/Http';

    private function __construct()
    {
    }

    public static function get(UriInterface $uri, array $headers = []): Request
    {
        return self::http11Request(HttpMethod::GET, $uri, '', $headers);
    }

    /**
     * Performs a Request::create() with some default headers
     *
     * @param HttpMethod   $method
     * @param UriInterface $uri
     * @param string       $data
     * @param array        $headers
     *
     * @return Request with headers :
     *                 Host: $uri->getHost()
     *                 Content-length: strlen($data)
     */
    public static function http11Request(
        HttpMethod $method,
        UriInterface $uri,
        string $data = '',
        array $headers = []
    ): Request {
        return Request::create(
            $method,
            $uri,
            $data,
            Headers::createFrom($headers)
                   ->host($uri->getHost())
                   ->contentLength(strlen($data))
                   ->userAgent(self::$userAgent)
                   ->date()
                   ->get()
        );
    }

    public static function head(UriInterface $uri, array $headers = []): Request
    {
        return self::http11Request(HttpMethod::HEAD, $uri, '', $headers);
    }

    public static function post(UriInterface $uri, array $data, array $headers = []): Request
    {
        return self::http11Request(
            HttpMethod::POST,
            $uri,
            http_build_query($data),
            ['Content-Type' => 'application/x-www-form-urlencoded'] + $headers
        );
    }

    public static function formPost(
        UriInterface $uri,
        array $data,
        array $headers = [],
        ?string $boundary = null,
    ): Request {
        $boundary ??= bin2hex(random_bytes(4));
        return self::http11Request(
            HttpMethod::POST,
            $uri,
            self::multipart($data, $boundary),
            [
                'Content-Type' => "multipart/form-data;boundary=\"$boundary\"",
            ] + $headers
        );
    }

    public static function multipart(array $data, string $boundary, string $contentDisposition = 'form-data'): string
    {
        return implode(
                "\n",
                array_map(
                    fn(string $fieldName, string $fieldValue) => <<<§
                        --$boundary
                        Content-Disposition: $contentDisposition; name="$fieldName"
                        
                        $fieldValue
                        §,
                    array_keys($data),
                    array_values($data)
                )
            ) . "\n--$boundary--";
    }

    public static function jsonPost(UriInterface $uri, array $data, array $headers = []): Request
    {
        return self::http11Request(
            HttpMethod::POST,
            $uri,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json; charset=UTF-8'] + $headers
        );
    }

    public static function put(UriInterface $uri, array $data, array $headers = []): Request
    {
        return self::http11Request(HttpMethod::PUT, $uri, http_build_query($data), $headers);
    }


    public static function jsonPut(UriInterface $uri, array $data, array $headers = []): Request
    {
        return self::http11Request(
            HttpMethod::PUT,
            $uri,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json; charset=UTF-8'] + $headers
        );
    }
}
