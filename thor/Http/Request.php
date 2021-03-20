<?php

namespace Thor\Http;

// Done : HTTP 1.1
use JetBrains\PhpStorm\Pure;

/** @see https://www.ibm.com/support/knowledgecenter/ssw_ibm_i_73/rzaie/rzaiewebdav.htm */
final class Request
{

    // HTTP 1.1
    public const GET = 'GET';
    public const POST = 'POST';

    public const PUT = 'PUT';
    public const PATCH = 'PATCH';
    public const DELETE = 'DELETE';

    public const HEAD = 'HEAD';
    public const TRACE = 'TRACE';
    public const CONNECT = 'CONNECT';
    public const OPTIONS = 'OPTIONS';

    // WEBDAV
    /** @see http://www.webdav.org/specs/rfc4918.html */
    public const MKCOL = 'MKCOL';
    public const COPY = 'COPY';
    public const MOVE = 'MOVE';
    public const PROPFIND = 'PROPFIND';
    public const PROPPATCH = 'PROPPATCH';
    public const LOCK = 'LOCK';
    public const UNLOCK = 'UNLOCK';

    private function __construct(
        private string $type,
        private array $headers,
        private string $body,
        private string $pathInfo,

        public bool $hasBody,
        public bool $responseHasBody,
        public bool $safe,
        public bool $idempotent,
        public bool $cache,
        public bool $html
    ) {
    }

    public static function createFromServer(): self
    {
        $type = strtoupper($_SERVER['REQUEST_METHOD']);
        $hasBody = in_array(
            $type,
            [self::POST, self::PUT, self::CONNECT, self::PATCH, self::MKCOL, self::PROPFIND, self::PROPPATCH]
        );
        $headers = self::getAllHeaders();
        $body = $hasBody ? file_get_contents('php://input') : '';
        $pathInfo = $_SERVER['PATH_INFO'] ?? '';
        $pathInfo = ($pathInfo === '' ? '/' : $pathInfo);

        return new self(
            $type,
            $headers,
            $body,
            $pathInfo,
            $hasBody,

            // response has a body
            in_array($type, [self::GET, self::POST, self::CONNECT, self::OPTIONS, self::PROPFIND]),
            // is safe
            in_array($type, [self::GET, self::HEAD, self::OPTIONS, self::PROPFIND]),
            // idempotent
            !in_array($type, [self::POST, self::CONNECT, self::PATCH]),
            // compatible with cache
            in_array($type, [self::GET, self::HEAD, self::POST, self::PROPFIND]),
            // compatible with html
            in_array($type, [self::GET, self::POST]),
        );
    }

    public static function getAllHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(
                    ' ',
                    '-',
                    ucwords(
                        strtolower
                        (
                            str_replace('_', ' ', substr($name, 5))
                        )
                    )
                )] = $value;
            }
        }
        return $headers;
    }

    public function getMethod(): string
    {
        return $this->type;
    }

    public function getPathInfo(): string
    {
        return $this->pathInfo;
    }

    public function getHeader(string $name, $default = null): array|string|null
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

    #[Pure]
    public function queryGet(
        string $name,
        $default = null
    ): string|array|null {
        return Server::get($name, $default);
    }

    #[Pure]
    public function postVariable(
        string $name,
        $default = null
    ): string|array|null {
        if ($this->type === self::POST) {
            return Server::post($name, $default);
        }

        return null;
    }

}
