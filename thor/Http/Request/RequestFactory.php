<?php

namespace Thor\Http\Request;

use Thor\Framework\Thor;
use Thor\Http\Web\Headers;
use Thor\Http\UriInterface;
use Thor\Common\Types\Strings;

/**
 *
 */

/**
 *
 */
final class RequestFactory
{

    public static string $userAgent = '{appName}-{version}/Http';

    private function __construct()
    {
    }

    /**
     * @param UriInterface $uri
     * @param array        $headers
     *
     * @return Request
     */
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
     * @param string       $contentType
     *
     * @return Request with headers :
     *                 Host: $uri->getHost()
     *                 Content-length: strlen($data)
     */
    public static function http11Request(
        HttpMethod $method,
        UriInterface $uri,
        string $data = '',
        array $headers = [],
        string $contentType = 'text/plain'
    ): Request {
        return Request::create(
            $method,
            $uri,
            $data,
            Headers::create()
                   ->host($uri->getHost())
                   ->contentType($contentType)
                   ->contentLength(strlen($data))
                   ->userAgent(
                       Strings::interpolate(
                           self::$userAgent,
                           [
                               'appName'     => Thor::appName(),
                               'version'     => Thor::version(),
                               'versionName' => Thor::versionName(),
                           ]
                       )
                   )
                   ->date()
                   ->merge($headers)
                   ->get()
        );
    }

    /**
     * @param UriInterface $uri
     * @param array        $headers
     *
     * @return Request
     */
    public static function head(UriInterface $uri, array $headers = []): Request
    {
        return self::http11Request(HttpMethod::HEAD, $uri, '', $headers);
    }

    /**
     * @param UriInterface $uri
     * @param array        $data
     * @param array        $headers
     *
     * @return Request
     */
    public static function post(UriInterface $uri, array $data, array $headers = []): Request
    {
        return self::http11Request(
            HttpMethod::POST,
            $uri,
            http_build_query($data),
            $headers,
            'application/x-www-form-urlencoded'
        );
    }

    /**
     * @param UriInterface $uri
     * @param array        $data
     * @param array        $headers
     * @param string|null  $boundary
     *
     * @return Request
     * @throws \Exception
     */
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
            $headers,
            "multipart/form-data;boundary=\"$boundary\""
        );
    }

    /**
     * @param array  $data
     * @param string $boundary
     * @param string $contentDisposition
     *
     * @return string
     */
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

    /**
     * @param UriInterface $uri
     * @param array        $data
     * @param array        $headers
     *
     * @return Request
     * @throws \JsonException
     */
    public static function jsonPost(UriInterface $uri, array $data, array $headers = []): Request
    {
        return self::http11Request(
            HttpMethod::POST,
            $uri,
            json_encode($data, JSON_THROW_ON_ERROR),
            $headers,
            'application/json; charset=UTF-8'
        );
    }

    /**
     * @param UriInterface $uri
     * @param array        $data
     * @param array        $headers
     *
     * @return Request
     */
    public static function put(UriInterface $uri, array $data, array $headers = []): Request
    {
        return self::http11Request(HttpMethod::PUT, $uri, http_build_query($data), $headers);
    }


    /**
     * @param UriInterface $uri
     * @param array        $data
     * @param array        $headers
     *
     * @return Request
     * @throws \JsonException
     */
    public static function jsonPut(UriInterface $uri, array $data, array $headers = []): Request
    {
        return self::http11Request(
            HttpMethod::PUT,
            $uri,
            json_encode($data, JSON_THROW_ON_ERROR),
            $headers,
            'application/json; charset=UTF-8'
        );
    }
}
