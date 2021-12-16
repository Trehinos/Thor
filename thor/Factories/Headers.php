<?php

namespace Thor\Factories;

use Exception;
use DateTimeInterface;
use DateTimeImmutable;

final class Headers
{

    public const AUTHORIZATION_BEARER = 'Bearer';
    public const TYPE_JSON = 'application/json; charset=UTF-8';
    public const TYPE_TEXT = 'text/plain';

    private array $headers = [];

    public function __construct()
    {
    }

    public static function create(array $headers = []): self
    {
        $headersObject = new self();
        $headersObject->headers = $headers;
        return $headersObject;
    }

    public function clear(): self
    {
        $this->headers = [];
        return $this;
    }

    public function get(): array
    {
        return $this->headers;
    }

    public function host(string $host): self
    {
        return self::merge(['Host' => $host]);
    }

    public function merge(array $headersToAdd): self
    {
        foreach ($headersToAdd as $name => $value) {
            if (array_key_exists($name, $this->headers)) {
                if (is_array($this->headers[$name])) {
                    if (is_array($value)) {
                        $this->headers[$name] = array_merge($this->headers[$name], $value);
                    } else {
                        $this->headers[$name][] = $value;
                    }
                } else {
                    if (is_array($value)) {
                        $this->headers[$name] = array_merge([$this->headers[$name]], $value);
                    } else {
                        $this->headers[$name] = [$this->headers[$name], $value];
                    }
                }
                continue;
            }
            $this->headers[$name] = $value;
        }
        return $this;
    }

    public function date(DateTimeInterface $dateTime = new DateTimeImmutable()): self
    {
        return self::merge(
            [
                'Date' => $dateTime->format(DateTimeInterface::RFC7231),
            ]
        );
    }

    public function userAgent(string $userAgent): self
    {
        return self::merge(['User-Agent' => $userAgent]);
    }

    public function contentType(string $mimeType): self
    {
        return self::merge(['Content-Type' => $mimeType]);
    }

    public function contentLength(int $length): self
    {
        return self::merge(['Content-Length' => $length]);
    }

    /**
     * @throws Exception
     */
    public function authorization(string $type, array $data): self
    {
        return self::merge(
            match ($type) {
                self::AUTHORIZATION_BEARER => ['Authorization' => "Bearer {$data['token']}"],
                default => throw new Exception("Invalid Authorization type $type")
            }
        );
    }

}
