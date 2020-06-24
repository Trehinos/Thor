<?php

namespace Thor\Http;

// Done : HTTP 1.1
use Thor\Globals;

/** @see https://www.ibm.com/support/knowledgecenter/ssw_ibm_i_73/rzaie/rzaiewebdav.htm */
final class Request
{

    // HTTP 1.1
    const GET = 'GET';
    const HEAD = 'HEAD';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const CONNECT = 'CONNECT';
    const OPTIONS = 'OPTIONS';
    const TRACE = 'TRACE';
    const PATCH = 'PATCH';

    // WEBDAV
    /** @see http://www.webdav.org/specs/rfc4918.html */
    const MKCOL = 'MKCOL';
    const COPY = 'COPY';
    const MOVE = 'MOVE';
    const PROPFIND = 'PROPFIND';
    const PROPPATCH = 'PROPPATCH';
    const LOCK = 'LOCK';
    const UNLOCK = 'UNLOCK';

    private bool $_hasBody;
    private bool $_responseHasBody;
    private bool $_safe;
    private bool $_idempotent;
    private bool $_cache;
    private bool $_html;

    private string $type;
    private array $headers;
    private string $body;
    private string $pathInfo;

    private function __construct(
        string $type,
        array $headers,
        string $body,
        string $pathInfo,

        bool $hasBody,
        bool $responseHasBody,
        bool $safe,
        bool $idempotent,
        bool $cache,
        bool $html
    ) {
        $this->type = $type;
        $this->headers = $headers;
        $this->body = $body;
        $this->pathInfo = $pathInfo;

        $this->_hasBody = $hasBody;
        $this->_responseHasBody = $responseHasBody;
        $this->_safe = $safe;
        $this->_idempotent = $idempotent;
        $this->_cache = $cache;
        $this->_html = $html;
    }

    public function getMethod(): string
    {
        return $this->type;
    }

    public function getPathInfo(): string
    {
        return $this->pathInfo;
    }

    public function getHeader(string $name, $default = null)
    {
        return $this->headers[$name] ?? $default;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function queryGet(string $name, $default = null)
    {
        return Globals::get($name, $default);
    }

    public function postVariable(string $name, $default = null)
    {
        if ($this->type === self::POST) {
            return Globals::post($name, $default);
        }

        return null;
    }

    public static function createFromServer()
    {
        $type = strtoupper($_SERVER['REQUEST_METHOD']);
        $hasBody = in_array(
            $type,
            [self::POST, self::PUT, self::CONNECT, self::PATCH, self::MKCOL, self::PROPFIND, self::PROPPATCH]
        );
        $headers = self::getAllHeaders();
        $body = $hasBody ? file_get_contents('php://input') : '';
        $pathInfo = $_SERVER['PATH_INFO'];
        $pathInfo = $pathInfo === '' ? '/' : $pathInfo;

        return new self(
            $type,
            $headers,
            $body,
            $pathInfo,

            $hasBody,                                                                                   // has a body
            in_array($type, [self::GET, self::POST, self::CONNECT, self::OPTIONS, self::PROPFIND]),     // response has a body
            in_array($type, [self::GET, self::HEAD, self::OPTIONS, self::PROPFIND]),                    // is safe
            !in_array($type, [self::POST, self::CONNECT, self::PATCH]),                                 // idempotent
            in_array($type, [self::GET, self::HEAD, self::POST, self::PROPFIND]),                       // compatible with cache
            in_array($type, [self::GET, self::POST]),                                                   // compatible with html
        );
    }

    public static function getAllHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

}
