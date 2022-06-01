<?php

namespace Thor\Http\Request;

/**
 * This enumeration lists all valid HTTP Methods as defined by RFC-7231.
 *
 * @link             https://datatracker.ietf.org/doc/html/rfc7231#section-4
 *
 * @see (PATCH, LINK, UNLINK) https://www.rfc-editor.org/rfc/rfc2068.html#section-19.6.1
 * @see (WebDav) https://www.iana.org/assignments/http-methods/http-methods.xhtml
 *
 * @package          Thor/Http/Request
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case HEAD = 'HEAD';
    case TRACE = 'TRACE';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';

    /**
     * @return bool
     */
    public function hasBody(): bool
    {
        return in_array($this, [HttpMethod::POST, HttpMethod::PUT, HttpMethod::CONNECT]);
    }

    /**
     * @return bool
     */
    public function responseHasBody(): bool
    {
        return in_array($this, [HttpMethod::GET, HttpMethod::POST, HttpMethod::CONNECT, HttpMethod::OPTIONS]);
    }

    /**
     * @return bool
     */
    public function isSafe(): bool
    {
        return in_array($this, [HttpMethod::GET, HttpMethod::HEAD, HttpMethod::OPTIONS, HttpMethod::TRACE]);
    }

    /**
     * @return bool
     */
    public function isIdempotent(): bool
    {
        return !in_array($this, [HttpMethod::POST, HttpMethod::CONNECT]);
    }

    /**
     * @return bool
     */
    public function compatibleWithCache(): bool
    {
        return in_array($this, [HttpMethod::GET, HttpMethod::HEAD, HttpMethod::POST]);
    }

    /**
     * @return bool
     */
    public function compatibleWithHtml(): bool
    {
        return in_array($this, [HttpMethod::GET, HttpMethod::POST]);
    }

}
