<?php

namespace Thor\Http\Request;

/** @link https://datatracker.ietf.org/doc/html/rfc7231#section-4 */
enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case HEAD = 'HEAD';
    case TRACE = 'TRACE';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';

    public function hasBody(): bool
    {
        return in_array($this, [HttpMethod::POST, HttpMethod::PUT, HttpMethod::CONNECT, HttpMethod::PATCH]);
    }

    public function responseHasBody(): bool
    {
        return in_array($this, [HttpMethod::GET, HttpMethod::POST, HttpMethod::CONNECT, HttpMethod::OPTIONS]);
    }

    public function isSafe(): bool
    {
        return in_array($this, [HttpMethod::GET, HttpMethod::HEAD, HttpMethod::OPTIONS]);
    }

    public function isIdempotent(): bool
    {
        return in_array($this, [HttpMethod::POST, HttpMethod::CONNECT, HttpMethod::PATCH]);
    }

    public function compatibleWithCache(): bool
    {
        return in_array($this, [HttpMethod::GET, HttpMethod::HEAD, HttpMethod::POST]);
    }

    public function compatibleWithHtml(): bool
    {
        return in_array($this, [HttpMethod::GET, HttpMethod::POST]);
    }

}
