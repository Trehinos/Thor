<?php

namespace Thor\Factories;

use Thor\Http\UriInterface;
use Thor\Http\Request\Request;
use Thor\Http\Request\HttpMethod;

class RequestFactory
{

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

    public static function get(UriInterface $uri, array $headers = []): Request
    {
        return Request::create(HttpMethod::GET, $uri, '', $headers);
    }

    public static function head(UriInterface $uri, array $headers = []): Request
    {
        return Request::create(HttpMethod::HEAD, $uri, '', $headers);
    }

    public static function post(UriInterface $uri, array $data, array $headers = []): Request
    {
        return Request::create(
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
        return Request::create(
            HttpMethod::POST,
            $uri,
            self::multipart($data, $boundary),
            [
                'Content-Type' => "multipart/form-data;boundary=\"$boundary\"",
            ] + $headers
        );
    }

    public static function jsonPost(UriInterface $uri, array $data, array $headers = []): Request
    {
        return Request::create(
            HttpMethod::POST,
            $uri,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json'] + $headers
        );
    }

    public static function put(UriInterface $uri, array $data, array $headers = []): Request
    {
        return Request::create(HttpMethod::PUT, $uri, http_build_query($data), $headers);
    }


    public static function jsonPut(UriInterface $uri, array $data, array $headers = []): Request
    {
        return Request::create(HttpMethod::PUT, $uri, json_encode($data, JSON_THROW_ON_ERROR), $headers);
    }
}
