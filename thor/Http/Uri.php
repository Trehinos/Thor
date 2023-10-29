<?php

namespace Thor\Http;

use Thor\Tools\Strings;
use JetBrains\PhpStorm\Pure;

/**
 * Holds and manages an URI.
 *
 * @package          Thor/Http
 * @copyright (2021) Sébastien Geldreich
 * @license          MIT
 */
class Uri implements UriInterface
{

    public const SCHEME_HTTP = 'http';
    public const SCHEME_HTTPS = 'https';
    public const SCHEME_FTP = 'ftp';
    public const SCHEME_SSH = 'ssh';

    /**
     * @param string      $scheme
     * @param string      $host
     * @param string|null $user
     * @param string|null $password
     * @param int|null    $port
     * @param string      $path
     * @param array       $queryArguments
     * @param string      $fragment
     */
    private function __construct(
        private string $scheme,
        private string $host = '',
        private ?string $user = null,
        private ?string $password = null,
        private ?int $port = null,
        private string $path = '',
        private array $queryArguments = [],
        private string $fragment = '',
    ) {
        $this->port ??= match ($this->scheme) {
            self::SCHEME_FTP   => 21,
            self::SCHEME_SSH   => 22,
            self::SCHEME_HTTP  => 80,
            self::SCHEME_HTTPS => 443,
            default            => null
        };
    }

    /**
     * Creates and returns an URI from $_SERVER globals.
     */
    public static function fromGlobals(): UriInterface
    {
        $host = null;
        $port = null;
        $queryArguments = [];
        $path = '';
        if (isset($_SERVER['HTTP_HOST'])) {
            [$host, $port] = self::extractHostAndPortFromAuthority($_SERVER['HTTP_HOST']);
        }
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestUriParts = explode('?', $_SERVER['REQUEST_URI'], 2);
            $path = $requestUriParts[0];
            if (($requestUriParts[1] ?? '') !== '') {
                parse_str($requestUriParts[1], $queryArguments);
            }
        }
        if (empty($queryArguments) && isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $queryArguments);
        }
        if (str_contains($path, '.php')) {
            $path = preg_replace('/^(.+\.php)/', '', $path);
        }

        return new self(
            !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
                ? self::SCHEME_HTTPS
                : self::SCHEME_HTTP,
            $host ?? $_SERVER['SERVER_NAME'] ?? $_SERVER['SERVER_ADDR'] ?? 'localhost',
            port: $port ?? $_SERVER['SERVER_PORT'] ?? null,
            path: $path,
            queryArguments: $queryArguments
        );
    }

    /**
     * @param string $authority
     *
     * @return array
     */
    private static function extractHostAndPortFromAuthority(string $authority): array
    {
        $uri = self::SCHEME_HTTP . "://$authority";
        $parts = parse_url($uri);
        if (false === $parts) {
            return [null, null];
        }

        $host = $parts['host'] ?? null;
        $port = $parts['port'] ?? null;

        return [$host, $port];
    }

    /**
     * Creates an URI from the specified string.
     */
    public static function create(string $url): self|false
    {
        $prefix = '';
        if (preg_match('%^(.*://\[[0-9:a-f]+])(.*?)$%', $url, $matches)) {
            /** @var array{0:string, 1:string, 2:string} $matches */
            $prefix = $matches[1];
            $url = $matches[2];
        }

        /** @var string */
        $encodedUrl = preg_replace_callback(
            '%[^:/@?&=#]+%usD',
            static function ($matches) {
                return urlencode($matches[0]);
            },
            $url
        );

        $result = parse_url($prefix . $encodedUrl);
        if ($result === false) {
            return false;
        }

        $map = array_map('urldecode', $result);
        $scheme = $map['scheme'] ?? '';
        $host = $map['host'] ?? null;
        $port = $map['port'] ?? null;
        $path = $map['path'] ?? '';
        if ($host === null && $port === null && str_contains($path, '/')) {
            $host = '';
            $path = Strings::split($path, '/', $host);
        }

        $queryArguments = [];
        if (($map['query'] ?? '') !== '') {
            parse_str($map['query'], $queryArguments);
        }
        return new self(
            $scheme,
            $host ?? '',
            $map['user'] ?? '',
            $map['pass'] ?? null,
            $port,
            ltrim($path, '/'),
            $queryArguments,
            $map['fragment'] ?? ''
        );
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function withScheme(string $scheme): static
    {
        return new self(
            $scheme,
            $this->host,
            $this->user,
            $this->password,
            $this->port,
            $this->path,
            $this->queryArguments,
            $this->fragment,
        );
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function withHost(string $host): static
    {
        return new self(
            $this->scheme,
            $host,
            $this->user,
            $this->password,
            $this->port,
            $this->path,
            $this->queryArguments,
            $this->fragment,
        );
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function withPort(?int $port = null): static
    {
        return new self(
            $this->scheme,
            $this->host,
            $this->user,
            $this->password,
            $port,
            $this->path,
            $this->queryArguments,
            $this->fragment,
        );
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function withPath(string $path): static
    {
        return new self(
            $this->scheme,
            $this->host,
            $this->user,
            $this->password,
            $this->port,
            $path,
            $this->queryArguments,
            $this->fragment,
        );
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function withQuery(array $queryArguments): static
    {
        return new self(
            $this->scheme,
            $this->host,
            $this->user,
            $this->password,
            $this->port,
            $this->path,
            $queryArguments,
            $this->fragment,
        );
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function withUserInfo(string $user, ?string $password = null): static
    {
        return new self(
            $this->scheme,
            $this->host,
            $user,
            $password,
            $this->port,
            $this->path,
            $this->queryArguments,
            $this->fragment,
        );
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function withFragment(string $fragment): static
    {
        return new self(
            $this->scheme,
            $this->host,
            $this->user,
            $this->password,
            $this->port,
            $this->path,
            $this->queryArguments,
            $fragment,
        );
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function __toString(): string
    {
        $query = Strings::prefix('?', $this->getQuery());
        $fragment = Strings::prefix('#', $this->getFragment());
        $path = $this->getPath();
        $authority = $path !== '' ? Strings::suffix($this->getAuthority(), '/') : $this->getAuthority();
        $scheme = Strings::suffix($this->getScheme(), '://');
        return "$scheme$authority$path$query$fragment";
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return http_build_query($this->queryArguments);
    }

    /**
     * @inheritDoc
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function getAuthority(): string
    {
        $port = Strings::prefix(':', $this->getPort());
        $userInfo = Strings::suffix($this->getUserInfo(), '@');
        return "$userInfo{$this->getHost()}$port";
    }

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): ?int
    {
        if (
            ($this->scheme === self::SCHEME_HTTP && $this->port === 80)
            ||
            ($this->scheme === self::SCHEME_HTTPS && $this->port === 443)
        ) {
            return null;
        }
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function getUserInfo(): string
    {
        return $this->user . Strings::prefix(':', $this->password);
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }
}
